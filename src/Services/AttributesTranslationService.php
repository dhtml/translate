<?php

namespace Dhtml\Translate\Services;

use Carbon\Carbon;
use Dhtml\Translate\LocaleAttribute;
use Flarum\Foundation\Paths;
use Flarum\Settings\SettingsRepositoryInterface;

class AttributesTranslationService
{
    public function __construct()
    {
        $this->settings = resolve(SettingsRepositoryInterface::class);
    }

    public function start()
    {
        echo "Build Translatable Attributes Start\n";

        $data = json_decode(file_get_contents(getSourceAttributesPath()));
        $this->parseLocaleData($data);

        echo "Build Translatable Attributes Finish\n";
    }

    function parseLocaleData($data)
    {
        foreach ($data as $key) {
           $value =  $this->settings->get($key) ?? null;

           if($value) {
               $this->cacheLocaleData($key,$value);
           }
        }
    }

    function isEnglish($string) {
        // Regular expression to match English characters and common punctuation
        return preg_match('/^[a-zA-Z0-9 .,!?\'"\-()]+$/', $string);
    }

    function cacheLocaleData($key, $value) {
        // Check if the record exists based on hkey
        $localeAttribute = LocaleAttribute::where('hkey', $key)->first();

        if (!$localeAttribute) {
            // Record does not exist, create a new one
            $localeAttribute = localeAttribute::create([
                "hkey" => $key,
                "original" => $value,
                "_translatable" => $this->isEnglish($value),
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]);
        }
    }


    public function translateAttributes()
    {
        $detectedLocale = getDetectedLocale();

        $attributes = LocaleAttribute::get();
        foreach ($attributes as $model) {
            $hkey = $model->hkey;
            $translatedData = $model->{"sub_" . $detectedLocale} ?? null;
            if(!$translatedData) {continue;} //not set

            $tdata = @json_decode($translatedData, true);
            if (isArrayEmptyValues($tdata)) {continue;} //not an array

            $value = $tdata['original'] ?? null;
            if(!$value) {continue;}
            $this->settings->set($hkey, $value);
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
