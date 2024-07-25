<?php

namespace Dhtml\Translate\Services;

use Carbon\Carbon;
use DateTime;
use Dhtml\Translate\Badge;
use Dhtml\Translate\Discussion;
use Dhtml\Translate\LocaleString;
use Dhtml\Translate\Page;
use Dhtml\Translate\Post;
use Dhtml\Translate\Tag;
use Flarum\Foundation\Paths;
use Symfony\Component\Yaml\Yaml;

class StringTranslationService
{
    public function __construct()
    {
    }

    public function start()
    {
        $file = getSourceStringLanguageFilePath();
        echo "Build Translatable Strings Start\n";

        $data = Yaml::parseFile($file);
        $this->parseLocaleData($data);

        echo "Build Translatable Strings Finish\n";
    }

    function parseLocaleData($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->parseLocaleData($value);
            } elseif (is_string($value)) {
                //$data[$key] = $this->cachetranslateText($value);
                $this->cacheLocaleData($value);
            }
        }
        return $data;
    }

    function isEnglish($string) {
        // Regular expression to match English characters and common punctuation
        return preg_match('/^[a-zA-Z0-9 .,!?\'"\-()]+$/', $string);
    }

    function cacheLocaleData($value) {
        if(empty($value)) {return;}
        $hash = md5($value); // Or use another hash function if needed

        $existingRecord = LocaleString::where('original', $value)->first();

        if (!$existingRecord) {
            $rawData = [
                "_hash" => $hash,
                "original" => $value,
                "_translatable" => $this->isEnglish($value),
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ];
            LocaleString::firstOrCreate($rawData);
        }
    }

    public function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'batch-translator-service.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }

}
