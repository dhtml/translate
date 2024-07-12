<?php

namespace Dhtml\Translate\Services;

use Carbon\Carbon;
use Dhtml\Translate\LocaleString;
use Flarum\Foundation\Paths;
use Symfony\Component\Yaml\Yaml;

class LanguageService
{
    public function __construct()
    {
        $this->localeService = new LocaleService();

        $this->locales = $this->localeService->selectEnabled();

        $this->currentLocale = $this->localeService->getCurrentLocale();

        $this->sourceFile = getSourceStringLanguageFilePath();
        $this->destinationFolder = getLocaleDestinationPath();
    }

    public function start()
    {

     //$this->translateLocale("en");
     //$this->translateLocale("fr");
     foreach ($this->locales as $locale) {
         $this->translateLocale($locale);
     }
    }

    protected function translateLocale($locale) {
        $filename = "{$locale}.yml";
        echo("==> Translating to $filename..");

        $destinationFile = $this->destinationFolder . "/$filename";

        if($locale==$this->currentLocale) {
            //default locale
            if (copy($this->sourceFile, $destinationFile)) {
                echo "done\n";
            } else {
                echo "failed\n";
            }
        } else {
            //translate
            if($this->translateLocaleFile($this->sourceFile, $destinationFile,$locale)) {
                echo "done\n";
            } else {
                echo "failed\n";
            }
        }
    }

    protected function translateLocaleFile($sourceFile, $targetFile, $targetLanguage)
    {
        $data = Yaml::parseFile($sourceFile);

        $translatedData = $this->translateArray($data,$targetLanguage);

        // Write the translated data to the target file
        $yaml = Yaml::dump($translatedData, 2, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        return file_put_contents($targetFile, $yaml);
    }

    protected function translateArray($data, $targetLanguage)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->translateArray($value,$targetLanguage);
            } elseif (is_string($value)) {
                $data[$key] = $this->translateText($value,$targetLanguage);
            }
        }
        return $data;
    }


    private function translateText($value, $language)
    {
        $item = (object) ["original"=>$value];
        $hash = EntityService::generateHash("string", $item);

        $model = LocaleString::where('_hash',$hash)->first();
        if(!$model) {return $value;}

        $translatedData = $model->{"sub_" . $language} ?? null;
        if(!$translatedData) {return $value;} //not set

        $tdata = @json_decode($translatedData, true);
        if (isArrayEmptyValues($tdata)) {return $value;} //not an array

        $newvalue = $tdata['original'] ?? null;
        if(!$newvalue) {return $value;}

        return $newvalue;
    }


    public function write($str) {
        echo "$str\n";
    }

    public function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'batch-translator-service.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }


}
