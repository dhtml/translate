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

        $response = [
            "success" => false,
            "source"=>$source,
            "locale" =>$locale,
            "content" => "", //translated html
            "locale" => $this->currentLocale, //detected locale
            $response['errorLevel'] = 0, //translation failure
            "error" => null
        ];

        if (!$this->isLocaleSupported($locale)) {
            $response['error'] = "$locale is not supported";
            return $response;
        }

        $this->translateText($source, $locale, $response);
        if ($response['success'] && !$this->containsHtmlTags($source)) {
            $response['content'] = strip_tags($response['content']);
        }

        /*
        if ($response['content'] == "") {
            $this->logInfo(["message" => "failure", "response" => $response]);
        }
        */

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

    private function translateText($text, $target, &$response)
    {
        sleep(5);

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

            curl_close($ch);

            //$this->logInfo(["rawResult"=>$result]);

            $result = @json_decode($result, true);

            $this->logInfo(["rawResult"=>$result]);

            //'rawResult' => '{"error":"Too many request limits violations"}

            if(is_null($result)) {
                //skipping criteria
                $response['success'] = false;
                $response['content'] = null;
                $response['errorLevel'] = 1; //translation skipped
                $response['error'] = "unable to translate";
            } else  if(isset($result['translatedText'])) {
                $response['success'] = true;
                $response['content'] = $result['translatedText'] ?? "";

                if($response['content']=="") {
                    $response['errorLevel'] = 1;
                    $response['error'] = "empty result";
                }
            } else  if(isset($result['error'])) {
                $response['success'] = false;
                $response['content'] = null;
                $response['errorLevel'] = 2; //serious failure
                $response['error'] = $result['error'];
            } else {
                $response['success'] = false;
                $response['content'] = null;
                $response['errorLevel'] = 2; //translation failure
                $response['error'] = "unknown failure";
            }
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['content'] = null;
            $response['errorLevel'] = 2; //exception, probably timeout
            $response['error'] = $e->getMessage();
        }
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
