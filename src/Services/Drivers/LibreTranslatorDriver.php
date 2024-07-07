<?php

namespace Dhtml\Translate\Services\Drivers;

use Dhtml\Translate\Services\LocaleService;
use Exception;
use Flarum\Foundation\Paths;

class LibreTranslatorDriver
{
    protected $maxLength = 2000;
    private $supportedLocalizations = ["ar", "az", "bg", "bn", "ca", "cs", "da", "de", "el", "en", "eo", "es", "et", "fa", "fi", "fr", "ga", "he", "hi", "hu", "id", "it", "ja", "ko", "lt", "lv", "ms", "nb", "nl", "pl", "pt", "ro", "ru", "sk", "sl", "sq", "sr", "sv", "th", "tl", "tr", "uk", "ur", "vi", "zh", "zt"];

    public function __construct($apikey, $rateLimit)
    {
        $this->apikey = $apikey;
        $this->rateLimit = $rateLimit;

        $this->supportedLocalizations = getTranslatableLocales("libre");

        $this->localeService = new LocaleService();
        $this->currentLocale = $this->localeService->getCurrentLocale();
    }

    /*
     * This will not return in less than 1.3 seconds, so this can only fired at
     * 1.3 x 60 = 78 -- libre allows maximum of 80 calls per minute
     */

    public function supportedLocales()
    {
        return $this->supportedLocalizations;
    }

    public function translateHTML($source, $locale, $source_language = null)
    {
        if ($source_language) {
            $this->setCurrentLocale($source_language);
        }

        $response = ["success" => false, "content" => "", //translated html
            "locale" => $this->currentLocale, //detected locale
            "error" => null,];

        if (!$this->isLocaleSupported($locale)) {
            $response['error'] = "$locale is not supported";
            return $response;
        }

        try {
            $tresult = $this->translateText($source, $locale);

            // Return the translated HTML
            $response['success'] = true;

            $output = $tresult['translatedText'];
            if (!$this->containsHtmlTags($source)) {
                $output = strip_tags($output);
            }

            $response['content'] = $output;

            if ($response['content'] == "") {
                $response['success'] = false;
                $response['error'] = "Failed to translate";
            }
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
            $this->logInfo("Libre API Failed: " . $e->getMessage());
        }

        if ($response['content'] == "") {
            $this->logInfo(["message" => "translation failed", "source" => $source, "locale" => $locale, "source_language" => $source_language]);
        }

        return $response;
    }

    public function setCurrentLocale($locale)
    {
        $this->currentLocale = $locale;
    }

    public function isLocaleSupported($locale)
    {
        return in_array($locale, $this->supportedLocalizations);
    }

    private function translateText($text, $target)
    {
        $response = [];

        $url = "https://libretranslate.com/translate";
        $data = ['q' => $text, 'source' => $this->currentLocale, //auto is failing drastically
            "format" => "html", 'target' => $target, 'api_key' => $this->apikey];

        try {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "Accept: application/json"]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            // Disable SSL verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $result = curl_exec($ch);

            if ($result === FALSE) {
                $error = curl_error($ch);
                curl_close($ch);
                trigger_error("cURL Error: $error", E_USER_ERROR);
            }

            curl_close($ch);

            $response = @json_decode($result, true);
        } catch (Exception $e) {
            $this->logInfo("curl error - " . $e->getMessage());
        }

        sleep(2);

        if (!isset($response['translatedText'])) {
            $response = ['translatedText' => ''];
        }

        return $response;
    }

    public function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'libre-translator-engine.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }

    protected function containsHtmlTags($string)
    {
        // Regular expression to detect HTML tags
        $pattern = '/<[^>]+>/';

        // Check if the pattern matches the string
        return preg_match($pattern, $string) === 1;
    }
}
