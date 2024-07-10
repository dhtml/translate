<?php

namespace Dhtml\Translate\Services;

use Dhtml\Translate\Badge;
use Dhtml\Translate\Discussion;
use Dhtml\Translate\Page;
use Dhtml\Translate\Post;
use Dhtml\Translate\Tag;
use Flarum\Foundation\Paths;


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
            $content = json_encode($data,JSON_UNESCAPED_UNICODE);
        } elseif (str_contains($contentType, 'application/vnd.api+json')) {
            $data = json_decode($content, true);
            $data = $this->translateApiData($data);
            $content = json_encode($data,JSON_UNESCAPED_UNICODE);
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
                    $this->localizeData($item);
                } else {
                    $this->searchAndTranslateAttributes($item);
                }
            }
        }
    }

    /**
     * Filter content according to locale
     * @param $item
     * @return void
     */
    public function localizeData(&$item)
    {
        $id = $item['id'];
        $model = null;

        switch ($item['type']) {
            case "posts":
                $model = Post::where('type', 'comment')->where('id', $id)->first();
                break;
            case "discussions":
                $model = Discussion::where('id', $id)->first();

                break;
            case "tags":
                $model = Tag::where('id', $id)->first();
                break;
            case "badges":
                $model = Badge::where('id', $id)->first();
                break;
            case "page":
                $model = Page::where('id', $id)->first();

                $item['attributes']['content'] = languageMenu($item['attributes']['content']);
                break;
            default:
                return;
        }

        //no model found
        if (!$model) {
            //$this->logInfo(["type"=>$item['type'],"error"=>"missing model","item"=>$item]);
            return;
        }

        $detectedLocale = getDetectedLocale();
        $locale = $model->_locale;

        //detected local is same as model local
        if ($detectedLocale == $locale) {
            //$this->logInfo(["type" => $item['type'], "error" => "original locale", "item" => $item]);
            return;
        }

        //attempt to get localized data from db
        $translatedData = $model->{"sub_" . $detectedLocale} ?? null;

        //localized data not found
        if (!$translatedData) {
            //$this->logInfo(["type"=>$item['type'],"error"=>"empty a","translatedata"=>$translatedData]);
            return;
        }

        $tdata = @json_decode($translatedData, true);

        //the translated data is empty -- error
        if (isArrayEmptyValues($tdata)) {
            //$this->logInfo(["type"=>$item['type'],"error"=>"empty b","translatedata"=>$translatedData,"item"=>$item]);
            return;
        }

        //translate item at this point
        switch ($item['type']) {
            case "posts":
                //$this->logInfo(["post",$tdata]);
                $item['attributes']['contentHtml'] = formatContentoutput(convertCustomBbcodeToHtml($tdata['contentHtml']));
                break;
            case "discussions":
                $item['attributes']['title'] = formatContentoutput($tdata['title']);
                break;
            case "tags":
                $model = Tag::where('id', $id)->first();
                $item['attributes']['name'] = formatContentoutput($tdata['name']);
                $item['attributes']['description'] = formatContentoutput($tdata['description']);
                break;
            case "badges":
                $item['attributes']['name'] = formatContentoutput(strip_tags($tdata['name']));
                break;
            case "page":
                $item['attributes']['title'] = formatContentoutput($tdata['title']);
                $item['attributes']['content'] = formatContentoutput(languageMenu($tdata['content']));
                break;
        }
    }

    public function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'dhtml-translator-content-filter.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }

}
