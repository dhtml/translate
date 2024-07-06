<?php

namespace Dhtml\Translate\Services\Drivers;

use Flarum\Foundation\Paths;
use Flarum\Settings\SettingsRepositoryInterface;
use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Translate\V2\TranslateClient;

class GoogleTranslatorDriver
{
    public function __construct($apikey, $rateLimit)
    {
        $this->apikey = $apikey;
        $this->rateLimit = $rateLimit;
    }

    public function isLocaleSupported($locale) {
        return true;
    }

    public function translateHTML($source,$locale) {
        $response = [
            "success" => false,
            "content" => "", //translated html
            "locale" => "en", //detected locale
            "error" => null,
        ];

        try {
            $translate = new TranslateClient([
                'key' => $this->apikey
            ]);

            $tresult = $translate->translate($source, [
                'target' => $locale
            ]);

            $response['success'] = true;
            $response['locale'] = $tresult['source'];
            $response['content'] = $tresult['text'];

        } catch (GoogleException $e) {
            $response['error'] = $e->getMessage();
            $this->logInfo("Google API Failed: " . $e->getMessage());
        }

        return $response;
    }

    public function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'google-translator-engine.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }

}
