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
        $id = $item['id'];
        $model = null;

        switch ($item['type']) {
            case "posts":
                $model = Post::where('type','comment')->where('id',$id)->first();
                break;
            case "discussions":
                $model = Discussion::where('id',$id)->first();

                break;
            case "tags":
                $model = Tag::where('id',$id)->first();
                break;
            case "badges":
                $model = Badge::where('id',$id)->first();
                break;
            case "page":
                $model = Page::where('id',$id)->first();
                break;
        }

        //no model found
        if(!$model) {return;}

        $detectedLocale = getDetectedLocale();
        $locale = $model->_locale;

        //detected local is same as model local
        if($detectedLocale == $locale) {return;}

        //attempt to get localized data from db
        $translatedData = $model->{"sub_".$detectedLocale} ?? null;

        //localized data not found
        if(!$translatedData) {return;}

        $tdata = @json_decode($translatedData,true);

        //the translated data is empty -- error
        if(isArrayEmptyValues($tdata)) {
            return;
        }

        //translate item at this point
        switch ($item['type']) {
            case "posts":
                //$this->logInfo(["post",$tdata]);
                $item['attributes']['contentHtml'] = $tdata['contentHtml'];
                break;
            case "discussions":
                $item['attributes']['title'] = $tdata['title'];
                break;
            case "tags":$model = Tag::where('id',$id)->first();
                $item['attributes']['name'] = $tdata['name'];
                $item['attributes']['description'] = $tdata['description'];
                break;
            case "badges":
                $item['attributes']['name'] = strip_tags($tdata['name']);
                break;
            case "page":
                $item['attributes']['title'] = $tdata['title'];
                $item['attributes']['content'] = $tdata['content'];
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
