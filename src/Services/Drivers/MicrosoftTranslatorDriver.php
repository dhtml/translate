<?php

namespace Dhtml\Translate\Services\Drivers;

use Dhtml\Translate\TranslateMicrosoft;
use Flarum\Foundation\Paths;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

class MicrosoftTranslatorDriver
{
    public function __construct($apikey, $region, $rateLimit)
    {
        $this->apikey = $apikey;
        $this->region = $region;
        $this->rateLimit = $rateLimit;

        $this->supportedLocalizations = getTranslatableLocales("microsoft");
    }

    /**
     * Make external api call
     *
     * @param $source
     * @param $locale
     * @param $source_local
     * @return string
     */
    protected function translationExternalCall($source, $faker=false) {
        $rawApiResponse = "[]";
        if($faker) {
            $rawApiResponse = <<<end
[ { "detectedLanguage": { "language": "en", "score": 1 }, "translations": [ { "text": "Hallo", "to": "af" }, { "text": "\u1230\u120b\u121d", "to": "am" }, { "text": "\u0645\u0631\u062d\u0628\u0627", "to": "ar" }, { "text": "\u09a8\u09ae\u09b8\u09cd\u0995\u09be\u09f0", "to": "as" }, { "text": "Salam", "to": "az" }, { "text": "\u0421\u04d9\u043b\u04d9\u043c", "to": "ba" }, { "text": "\u0417\u0434\u0440\u0430\u0432\u0435\u0439\u0442\u0435", "to": "bg" }, { "text": "\u0939\u0947\u0932\u094b", "to": "bho" }, { "text": "\u09a8\u09ae\u09b8\u09cd\u0995\u09be\u09b0", "to": "bn" }, { "text": "\u0f41\u0fb1\u0f7c\u0f51\u0f0b\u0f56\u0f51\u0f7a\u0f0b\u0f58\u0f7c\u0f0d", "to": "bo" }, { "text": "\u0913\u0907", "to": "brx" }, { "text": "Zdravo", "to": "bs" }, { "text": "Hola", "to": "ca" }, { "text": "Dobr\u00fd den", "to": "cs" }, { "text": "Helo", "to": "cy" }, { "text": "Hej", "to": "da" }, { "text": "Hallo", "to": "de" }, { "text": "\u0928\u092e\u0938\u094d\u0924\u0947", "to": "doi" }, { "text": "Witaj", "to": "dsb" }, { "text": "\u0790\u07a6\u078d\u07a7\u0789\u07b0", "to": "dv" }, { "text": "\u0393\u03b5\u03b9\u03b1 \u03c3\u03b1\u03c2", "to": "el" }, { "text": "Hello", "to": "en" }, { "text": "Hola", "to": "es" }, { "text": "Tere", "to": "et" }, { "text": "Kaixo", "to": "eu" }, { "text": "\u0633\u0644\u0627\u0645", "to": "fa" }, { "text": "Hei", "to": "fi" }, { "text": "Hello po", "to": "fil" }, { "text": "Bula", "to": "fj" }, { "text": "Hall\u00f3", "to": "fo" }, { "text": "Bonjour", "to": "fr" }, { "text": "All\u00f4", "to": "fr-CA" }, { "text": "Dia duit", "to": "ga" }, { "text": "Sa\u00fados", "to": "gl" }, { "text": "\u0939\u0945\u0932\u094b", "to": "gom" }, { "text": "\u0a95\u0ac7\u0aae \u0a9b\u0acb", "to": "gu" }, { "text": "Barka", "to": "ha" }, { "text": "\u05e9\u05dc\u05d5\u05dd", "to": "he" }, { "text": "\u0928\u092e\u0938\u094d\u0915\u093e\u0930", "to": "hi" }, { "text": "\u091c\u094b\u0939\u093e\u0930", "to": "hne" }, { "text": "Pozdrav", "to": "hr" }, { "text": "Halo", "to": "hsb" }, { "text": "Bonjou\/Bonswa", "to": "ht" }, { "text": "\u00dcdv\u00f6zl\u00f6m", "to": "hu" }, { "text": "\u0548\u0572\u057b\u0578\u0582\u0575\u0576", "to": "hy" }, { "text": "Halo", "to": "id" }, { "text": "Nn\u1ecd\u1ecd", "to": "ig" }, { "text": "Haluu", "to": "ikt" }, { "text": "Hall\u00f3", "to": "is" }, { "text": "Ciao", "to": "it" }, { "text": "\u140a\u1403\u2019", "to": "iu-Cans" }, { "text": "aingai", "to": "iu-Latn" }, { "text": "\u3053\u3093\u306b\u3061\u306f", "to": "ja" }, { "text": "\u10d2\u10d0\u10db\u10d0\u10e0\u10ef\u10dd\u10d1\u10d0", "to": "ka" }, { "text": "\u0421\u04d9\u043b\u0435\u043c\u0435\u0442\u0441\u0456\u0437 \u0431\u0435", "to": "kk" }, { "text": "\u179f\u17bd\u179f\u17d2\u178f\u17b8", "to": "km" }, { "text": "Rojba\u015f", "to": "kmr" }, { "text": "\u0cb9\u0cb2\u0ccb", "to": "kn" }, { "text": "\uc548\ub155\ud558\uc138\uc694", "to": "ko" }, { "text": "\u06c1\u06cc\u065b\u0644\u0648", "to": "ks" }, { "text": "\u0633\u06b5\u0627\u0648", "to": "ku" }, { "text": "\u0421\u0430\u043b\u0430\u043c", "to": "ky" }, { "text": "Mbote", "to": "ln" }, { "text": "\u0eaa\u0eb0\u0e9a\u0eb2\u0e8d\u0e94\u0eb5", "to": "lo" }, { "text": "Labas", "to": "lt" }, { "text": "Oli otya", "to": "lug" }, { "text": "Labdien", "to": "lv" }, { "text": "\u4f60\u597d", "to": "lzh" }, { "text": "\u0915\u0940 \u092f\u094c", "to": "mai" }, { "text": "Hello", "to": "mg" }, { "text": "Kia ora", "to": "mi" }, { "text": "\u0417\u0434\u0440\u0430\u0432\u043e", "to": "mk" }, { "text": "\u0d39\u0d32\u0d4b", "to": "ml" }, { "text": "\u0421\u0430\u0439\u043d \u0443\u0443", "to": "mn-Cyrl" }, { "text": "\u1830\u1820\u1836\u1822\u1828 \u182a\u1820\u1836\u1822\u1828\u180e\u1820", "to": "mn-Mong" }, { "text": "\uabc8\uabe8\uabd4\uabe8\uabdd\uabd6\uabd4\uabe4", "to": "mni" }, { "text": "\u0928\u092e\u0938\u094d\u0915\u093e\u0930", "to": "mr" }, { "text": "Helo", "to": "ms" }, { "text": "\u0126ello", "to": "mt" }, { "text": "Nyob zoo", "to": "mww" }, { "text": "\u101f\u1032\u101c\u102d\u102f", "to": "my" }, { "text": "Hei", "to": "nb" }, { "text": "\u0928\u092e\u0938\u094d\u0924\u0947", "to": "ne" }, { "text": "Hallo", "to": "nl" }, { "text": "Dumela", "to": "nso" }, { "text": "Halo", "to": "nya" }, { "text": "\u0b28\u0b2e\u0b38\u0b4d\u0b15\u0b3e\u0b30", "to": "or" }, { "text": "Hadi", "to": "otq" }, { "text": "\u0a38\u0a24\u0a3f \u0a36\u0a4d\u0a30\u0a40 \u0a05\u0a15\u0a3e\u0a32", "to": "pa" }, { "text": "Witam", "to": "pl" }, { "text": "\u0633\u0644\u0627\u0645", "to": "prs" }, { "text": "\u0633\u0644\u0627\u0645", "to": "ps" }, { "text": "Ol\u00e1", "to": "pt" }, { "text": "Ol\u00e1;", "to": "pt-PT" }, { "text": "Bun\u0103 ziua", "to": "ro" }, { "text": "\u041f\u0440\u0438\u0432\u0435\u0442", "to": "ru" }, { "text": "Mwaramutse", "to": "run" }, { "text": "Muraho", "to": "rw" }, { "text": "\u06be\u064a\u0644\u0648", "to": "sd" }, { "text": "\u0d86\u0dba\u0dd4\u0db6\u0ddd\u0dc0\u0db1\u0dca", "to": "si" }, { "text": "Dobr\u00fd de\u0148", "to": "sk" }, { "text": "Zdravo", "to": "sl" }, { "text": "M\u0101l\u014d", "to": "sm" }, { "text": "Hesi", "to": "sn" }, { "text": "Haye", "to": "so" }, { "text": "P\u00ebrsh\u00ebndetje", "to": "sq" }, { "text": "\u0417\u0434\u0440\u0430\u0432\u043e", "to": "sr-Cyrl" }, { "text": "Zdravo", "to": "sr-Latn" }, { "text": "Dumela", "to": "st" }, { "text": "Hej", "to": "sv" }, { "text": "Hujambo", "to": "sw" }, { "text": "\u0bb9\u0bb2\u0bcb", "to": "ta" }, { "text": "\u0c39\u0c32\u0c4b", "to": "te" }, { "text": "\u0e2a\u0e27\u0e31\u0e2a\u0e14\u0e35", "to": "th" }, { "text": "\u1230\u120b\u121d", "to": "ti" }, { "text": "Salam", "to": "tk" }, { "text": "nuqneH", "to": "tlh-Latn" }, { "text": "\uf8db\uf8e5\uf8df\uf8db\uf8d4\uf8d6", "to": "tlh-Piqd" }, { "text": "Dumela", "to": "tn" }, { "text": "Malo e lelei", "to": "to" }, { "text": "Merhaba", "to": "tr" }, { "text": "\u0421\u04d9\u043b\u0430\u043c", "to": "tt" }, { "text": "Iaorana", "to": "ty" }, { "text": "\u064a\u0627\u062e\u0634\u0649\u0645\u06c7 \u0633\u06d5\u0646", "to": "ug" }, { "text": "\u041f\u0440\u0438\u0432\u0456\u0442", "to": "uk" }, { "text": "\u06c1\u06cc\u0644\u0648", "to": "ur" }, { "text": "Salom", "to": "uz" }, { "text": "Xin ch\u00e0o", "to": "vi" }, { "text": "Molo", "to": "xh" }, { "text": "B\u00e1wo", "to": "yo" }, { "text": "Hola", "to": "yua" }, { "text": "\u4f60\u597d", "to": "yue" }, { "text": "\u4f60\u597d", "to": "zh-Hans" }, { "text": "\u4f60\u597d", "to": "zh-Hant" }, { "text": "Sawubona", "to": "zu" } ] } ]
end;
        } else {

            //sleep(10);
            $endpoint = 'https://api.cognitive.microsofttranslator.com/translate';
            $resourceKey = $this->apikey;
            $region = $this->region; // e.g., 'westus2'

            $client = new Client();
            $uuid = Uuid::uuid4()->toString(); // Generate a UUID for X-ClientTraceId

            try {
                $response = $client->request('POST', $endpoint, [
                    'query' => [
                        'api-version' => '3.0',
                        'to' => $this->supportedLocalizations
                    ],
                    'headers' => [
                        'Ocp-Apim-Subscription-Key' => $resourceKey,
                        'Ocp-Apim-Subscription-Region' => $region,
                        'Content-Type' => 'application/json',
                        'X-ClientTraceId' => $uuid
                    ],
                    'json' => [
                        ['text' => $source]
                    ]
                ]);

                $rawApiResponse = $response->getBody();

            } catch (\Exception $e) {
                trigger_error("Microsoft translator failed due to " . $e->getMessage());
                echo "Exiting";
                exit();
            }

        }

        return $rawApiResponse;
    }

    /**
     * @param $jsonData
     * @return array
     *
     * Array
     * (
     * [locale] => en
     * [translations] => Array
     * (
     * [sub_af] => Hallo
     * [sub_am] => ሰላም
     * [sub_ar] => مرحبا
     * [sub_as] => নমস্কাৰ
     * )
     * )
     */
    protected function processExternalResult($jsonData) {
        $data = json_decode($jsonData, true);

        $result = [
            "locale" => $data[0]["detectedLanguage"]["language"],
            "translations" => []
        ];

        $result["translations"]["sub_en"] = "";

        if(isset($data[0]["translations"])) {
            foreach ($data[0]["translations"] as $tr) {
                $locale = $tr['to'];
                if (!in_array($locale, $this->supportedLocalizations)) {
                    continue;
                }
                $result["translations"]["sub_" . $locale] = $tr['text'];
            }
        } else {
            echo "..failed..";
            exit();
        }

        return $result;
    }

    protected function translateText($source, $locale,$source_local="auto") {
        $jsonData = $this->translationExternalCall($source);
        $result = $this->processExternalResult($jsonData);

        return $result;
    }

    public function translateHTML($source, $locale,$source_local="auto")
    {
        $hash = md5($source);

        if(strlen($source)>5000) {
            return $this->emptyResult($hash,$source,$locale);
        }

        $cache = TranslateMicrosoft::query()->where("hash", $hash)
            ->first();
        if ($cache) {
            return $this->translateHTMLResult($cache, "local", $locale);
        }

        $tdata = $this->translateText($source,$locale,$source_local);

        $inputData = [
            "hash" => $hash,
            "source" => $source,
            "locale" => $tdata['locale'], //detected locale
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ];

        foreach($tdata['translations'] as $key=>$value) {
            $inputData[$key] = $value;
        }

            $cache = TranslateMicrosoft::firstOrCreate($inputData);

        return $this->translateHTMLResult($cache, "remote", $locale);
    }

    protected function emptyResult($hash,$source,$locale)
    {
        $cache = (object) [
            "id" => -1,
            "hash" => $hash,
            "source" => $source,
            "locale" => "en",
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ];

        return $this->translateHTMLResult($cache, "local", $locale);
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
    protected function translateHTMLResult($t, $mode, $locale)
    {
        $translation = $t->{"sub_".$locale} ?? "";
        $response = [];
        $response['id'] = $t->id;
        $response['characters'] = strlen($t->source);
        $response['from_locale'] = $t->locale;
        $response['to_locale'] = $locale;
        $response['source'] = $t->source;
        $response['translation'] = $translation;
        $response['created_at'] = $t->created_at;
        $response['updated_at'] = $t->updated_at;
        $response['error_level'] = 0;
        $response['error_log'] = "";
        $response['mode'] = $mode;

        return (object)$response;
    }

    public function isLocaleSupported($locale)
    {
        return true;
    }


    public function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'microsoft-translator-engine.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }

}
