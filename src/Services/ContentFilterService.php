<?php

namespace Dhtml\Translate\Services;

use Carbon\Carbon;
use Dhtml\Translate\Translate;
use Flarum\Foundation\Application;
use Flarum\Foundation\Paths;
use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


class ContentFilterService
{

    public function __construct()
    {
        $this->localeService = new LocaleService();

        $this->currentLocale = $this->localeService->getCurrentLocale();

        $this->translatorService = new TranslatorService();
        $this->translationEngine = $this->translatorService->getTranslationEngine();
    }

    public function modifyContent(string $content, string $contentType): string
    {
        //$this->logger->info('Current locale: ' . $this->locale);

        if (str_contains($contentType, 'text/html')) {
            $content = $this->translatePage($content);
        } elseif (str_contains($contentType, 'application/json')) {
            $data = json_decode($content, true);
            $data = $this->translateApiData($data);
            $content = json_encode($data);
        } elseif (str_contains($contentType, 'application/vnd.api+json')) {
            $data = json_decode($content, true);
            $data = $this->translateApiData($data);
            $content = json_encode($data);
        }

        return $content;
    }

    public function translatePage($content)
    {
        return $content;
    }

    public function translateApiData($data)
    {
        $this->searchAndTranslateAttributes($data);
        return $data;
    }


    public function searchAndTranslateAttributes(&$array)
    {
        foreach ($array as &$item) {
            if (is_array($item)) {
                if (isset($item['type']) && isset($item['attributes'])) {

                    switch ($item['type']) {
                        case "page":
                            $tdata = $this->localizeEntityData($item,
                                [
                                    "title" => $item['attributes']['title'],
                                    "content" => $item['attributes']['content'],
                                ]);

                            $item['attributes']['title'] = $tdata['title'];
                            $item['attributes']['content'] = $tdata['content'];
                            break;
                        case "discussions":
                            $tdata = $this->localizeEntityData($item,
                                [
                                    "title" => $item['attributes']['title'],
                                ]);
                            $item['attributes']['title'] = $tdata['title'];
                            break;
                        case "tags":
                            $tdata = $this->localizeEntityData($item,
                                [
                                    "name" => $item['attributes']['name'],
                                    "description" => $item['attributes']['description'],
                                ]);
                            $item['attributes']['name'] = $tdata['name'];
                            $item['attributes']['description'] = $tdata['description'];
                            break;

                        case "posts":
                            if (!empty($item['attributes']['contentHtml']) && is_string($item['attributes']['contentHtml'])) {
                                $tdata = $this->localizeEntityData($item,
                                    [
                                        "contentHtml" => $item['attributes']['contentHtml'],
                                    ]);

                                $item['attributes']['contentHtml'] = $tdata['contentHtml'];
                            }
                            break;

                        case "badges":
                            $tdata = $this->localizeEntityData($item,
                                [
                                    "name" => $item['attributes']['name'],
                                ]);
                            $item['attributes']['name'] = strip_tags($tdata['name']);
                            break;

                        case "users":
                        case "userBadges":
                            //no action
                            break;
                        default:
                            //$this->logInfo($item);
                    }
                } else {
                    $this->searchAndTranslateAttributes($item);
                }
            }
        }
    }

    protected function isTooLong($array) {
        foreach ($array as $key => $value) {
            if (strlen($value) > 5000) {
                return true; // There is at least one value exceeding 5000 characters
            }
        }
        return false; // No value exceeds 5000 characters
    }

    public function localizeEntityData($item, $data) {
        $entity = $item['type'] . '-' . $item['id'];

        $original = json_encode($data);

        $hash = md5($original);

        //this is used for storing new conrents too
        $locale = $this->localeService->getResolvedLocale(); //original locale

        /*
        $this->logInfo([
            "locale"=>$locale,
        ]);
        */


        //dont accept empty values
        if (empty(array_filter($data))) {
            return $this->formatLocalizedResponse($original);
        }

            /*
            if($this->translationEngine->name == 'microsoft' && $this->isTooLong($data)) {
                return $this->formatLocalizedResponse($original); //too long
            }
            */

        $translate = Translate::where('entity', $entity)->first();

        $status = "new";

        if(!$translate) {
            //save data for the first time

            //content filter spoof
            if($locale!="en") {return $this->formatLocalizedResponse($original);}

            $translate = Translate::firstOrNew([
                "entity" => $entity,
            ], [
                "entity" => $entity,
                "original" => $original,
                "locale" => $locale, // locale of content, original posting but translation engine can change this
                "pointer" => 0,
                "translated" => 0,
                "outdated" => 0,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]);
            $translate->save();

            $result = $original;

            $status = "saved";
        } else {
            //fetch stored data (or update data)
            if ($this->currentLocale==$translate->locale && $hash != $translate->hash) {
                //original source has changed, must update
                $translate->outdated = 1;
                $translate->hash = $hash;
                $translate->pointer = 0;
                $translate->original = $original;
                $translate->updated_at = Carbon::now();
                $translate->save();
                $result = $original;
                $status = "hash mismatch";
            } else {
                //attempt to fetch data
                $key = "sub_{$locale}";
                if (isset($translate->{$key}) && strlen($translate->{$key}) > 3) {
                    $result = $translate->{$key};
                    $status = "resolved with $key";
                } else {
                    $result = $original; //translation outdated
                    $status = "revert to orig";
                }
            }
        }

        /*
        $this->logInfo([
            "locale"=>$locale,
            "status"=>$status,
            $result,
        ]);
        */


        return $this->formatLocalizedResponse($result);
    }

    protected function formatLocalizedResponse($result) {
        $result = (array)json_decode($result);

        //decode html
        foreach ($result as $key => &$value) {
            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML401, 'UTF-8');
        }
        return $result;
    }

    public function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'dhtml-translator-content-filter.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }

}
