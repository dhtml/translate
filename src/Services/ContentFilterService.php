<?php

namespace Dhtml\Translate\Services;

use Carbon\Carbon;
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
    public function localizeData(&$item) {
        switch ($item['type']) {
            case "posts":

                break;
            case "discussions":

                break;
            case "tags":
                break;
            case "badges":
                break;
            case "page":
                break;
            case "users":
            case "userBadges":
            default:
                return;
        }

        //$modelData = $this->retrieveEntityData($item);
        //$type = $item['type'];
        //$type = $item['type'];

        $detectedLocale = getDetectedLocale();
        //$locale = $item->_locale;

        $this->logInfo($item);
        //$this->logInfo([$detectedLocale,$locale]);
    }

    protected function retrieveEntityData($item) {
        $id = $item['id'];$type = $item['type'];

        /*
        $items = Tag::get();
        $items = Page::get();
        $items = Discussion::get();
        $items = Post::where('type','comment')->orderBy('id', $dir)->get();
        $items = Discussion::get();
        */

    }

    public function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'dhtml-translator-content-filter.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }

}
