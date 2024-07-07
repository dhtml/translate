<?php

namespace Dhtml\Translate\Services;

use Carbon\Carbon;
use Dhtml\Translate\Services\Drivers\GoogleTranslatorDriver;
use Dhtml\Translate\Services\Drivers\LibreTranslatorDriver;
use Dhtml\Translate\Services\Drivers\MicrosoftTranslatorDriver;
use Dhtml\Translate\TranslateCache;
use Flarum\Foundation\Application;
use Flarum\Settings\SettingsRepositoryInterface;

class TranslatorService
{
    public $maxLength = 5000; //maximum character length in a single batch
    public $maximumRateLimit = 0;

    public function __construct()
    {
        $this->settings = resolve(SettingsRepositoryInterface::class);
        $this->app = resolve(Application::class);
        $this->maximumRateLimit = $this->settings->get('dhtml-translate.rate-limit');

        $this->localeService = new LocaleService();
        $this->enabledLocales = $this->localeService->selectEnabled();

        $this->translationEngineName = $this->settings->get('dhtml-translate.plugin.engine');


        switch ($this->translationEngineName) {
            case "google":
                $this->apikey = $this->settings->get('dhtml-translate.googleApiKey');
                $this->translationEngine = new GoogleTranslatorDriver($this->apikey, $this->maximumRateLimit);
                break;
            case "microsoft":
                $this->apikey = $this->settings->get('dhtml-translate.microsoftApiKey');
                $this->translationEngine = new MicrosoftTranslatorDriver($this->apikey, $this->maximumRateLimit);
                break;
            case "libre":
                $this->apikey = $this->settings->get('dhtml-translate.libreApiKey');
                $this->maxLength = 2000;
                $this->translationEngine = new LibreTranslatorDriver($this->apikey, $this->maximumRateLimit);
                break;
            default:
                trigger_error("Translation driver not available");
        }
    }

    public function getTranslationEngine() {
        return (object) [
            "name"=>$this->translationEngineName,
            "driver" => $this->translationEngine,
            "maxRateLimit" => $this->maximumRateLimit,
        ];
    }

    public function isLocaleSupported($locale) {
        return $this->translationEngine->isLocaleSupported($locale);
    }

    public function translateHTML($source, $locale, $source_language = "en")
    {
        if($this->translationEngineName=="microsoft") {
            return $this->translationEngine->translateHTML($source, $locale);
        }

        $hash = md5($source . $locale);

        $cache = TranslateCache::query()->where("source", $source)
            ->where("to_locale", $locale)
            ->first();
        if ($cache) {
            return $this->translateHTMLResult($cache, "local");
        }

        $translatedHtml = '';


        // Split the HTML content into chunks based on driver
        $chunks = str_split($source, $this->maxLength);

        $chunk_size = count($chunks);
        $characters = strlen($source);

        $from_locale = "";
        // Translate each chunk and combine the results

        $error_level = 0;
        $error_log = null;

        foreach ($chunks as $chunk) {
            $response = $this->translationEngine->translateHTML($chunk, $locale, $source_language);
            if (!$response['success']) {
                $error_level = $response['errorLevel'] ?? 1;
                $error_log = $response['error'] ?? null;
                //trigger_error("Unable to translate data due to ".$response['error']);
                break;
            }
            $from_locale = $response['locale'];
            $translatedHtml .= $response['content'];
        }

        $rawData = [
            "id" => -1,
            "hash" => $hash,
            "source" => $source,
            "to_locale" => $locale,
            "chunk_size" => $chunk_size,
            "error_level" => $error_level,
            "error_log" => $error_log,
            "characters" => $characters,
            "from_locale" => $from_locale,
            "translation" => $translatedHtml,
            "translator" => $this->translationEngineName,
            "mode" => "remote",
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ];

        if($error_level==0) {
            $cache = TranslateCache::firstOrCreate($rawData);
            return $this->translateHTMLResult($cache, "remote");
        } else {
            return (object) $rawData;
        }
    }

    /*
     * stdClass Object
     * (
     * [id] => 2
     * [characters] => 20
     * [chunk_size] => 1
     * [hash] => d9c6d7f4bf200a95a1ac31f9ee034c61
     * [from_locale] => en
     * [to_locale] => ar
     * [source] => Happy sunday brother
     * [translation] => صباح الخير
     * [created_at] => 2024-07-02T01:53:10.000000Z
     * [updated_at] => 2024-07-02T01:53:10.000000Z
     * [mode] => local
     * )
     */
    protected function translateHTMLResult($cacheData, $mode)
    {
        $response = $cacheData->toArray();

        $response['mode'] = $mode;
        return (object)$response;
    }


}
