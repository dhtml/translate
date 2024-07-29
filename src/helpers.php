<?php

use Flarum\Foundation\Paths;
use Flarum\Http\RequestUtil;
use Flarum\Locale\LocaleManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Flarum\Formatter\Formatter;

function getSourceStringLanguageFilePath() {
    return __DIR__."/../../translated/language/source.yml";
}

function getSourceAttributesPath() {
    return __DIR__ . "/../../translated/language/attributes.json";
}

function getLocaleDestinationPath() {
    //return __DIR__ . "/../../translate-localization/locale";
    return __DIR__ . "/../../translated/locale";
}

if(!function_exists("convertBbcodeToHtml")) {
    function convertBbcodeToHtml($bbcode)
    {
        // Get the Formatter instance
        $formatter = resolve(Formatter::class);

        // Use the Formatter to parse and render the BBCode to HTML
        $html = $formatter->render($formatter->parse($bbcode));

        return $html;
    }
}

if(!function_exists("parseHTMLData")) {
    function parseHTMLData($string)
    {
        // Define the regex pattern to match BBCode tags
        $pattern = '/\[([a-zA-Z]+)(?:=[^\]]+)?\](.*?)\[\/\1\]/s';

        // Callback function to replace BBCode with HTML
        $callback = function ($matches) {
            $bbcode = $matches[0];
            $html = convertBbcodeToHtml($bbcode);
            return $html;
        };

        // Replace BBCode with HTML in the string
        $result = preg_replace_callback($pattern, $callback, $string);

        return $result;
    }
}

function languageMenu($html) {
    // Replace BBCode with HTML in the string
    $replace = '<div class="langlist langlist-large hlist">'
        . '<ul>'
        . '<li><a href="https://ar.africoders.com/" lang="ar" title="Arabic"><bdi dir="rtl">العربية</bdi></a></li>'
        . '<li><a href="https://az.africoders.com/" lang="az" title="Azərbaycan">Azərbaycan</a></li>'
        . '<li><a href="https://bg.africoders.com/" lang="bg" title="Български">Български</a></li>'
        . '<li><a href="https://bn.africoders.com/" lang="bn" title="বাংলা">বাংলা</a></li>'
        . '<li><a href="https://ca.africoders.com/" lang="ca" title="Català">Català</a></li>'
        . '<li><a href="https://cs.africoders.com/" lang="cs" title="Čeština">Čeština</a></li>'
        . '<li><a href="https://da.africoders.com/" lang="da" title="Dansk">Dansk</a></li>'
        . '<li><a href="https://de.africoders.com/" lang="de" title="Deutsch">Deutsch</a></li>'
        . '<li><a href="https://el.africoders.com/" lang="el" title="Ελληνικά">Ελληνικά</a></li>'
        . '<li><a href="https://en.africoders.com/" lang="en" title="English">English</a></li>'
        . '<li><a href="https://eo.africoders.com/" lang="eo" title="Esperanto">Esperanto</a></li>'
        . '<li><a href="https://es.africoders.com/" lang="es" title="Español">Español</a></li>'
        . '<li><a href="https://et.africoders.com/" lang="et" title="Eesti">Eesti</a></li>'
        . '<li><a href="https://fa.africoders.com/" lang="fa" title="فارسی"><bdi dir="rtl">فارسی</bdi></a></li>'
        . '<li><a href="https://fi.africoders.com/" lang="fi" title="Suomi">Suomi</a></li>'
        . '<li><a href="https://fr.africoders.com/" lang="fr" title="Français">Français</a></li>'
        . '<li><a href="https://ga.africoders.com/" lang="ga" title="Gaeilge">Gaeilge</a></li>'
        . '<li><a href="https://he.africoders.com/" lang="he" title="עברית"><bdi dir="rtl">עברית</bdi></a></li>'
        . '<li><a href="https://hi.africoders.com/" lang="hi" title="हिन्दी">हिन्दी</a></li>'
        . '<li><a href="https://hu.africoders.com/" lang="hu" title="Magyar">Magyar</a></li>'
        . '<li><a href="https://id.africoders.com/" lang="id" title="Bahasa Indonesia">Bahasa Indonesia</a></li>'
        . '<li><a href="https://it.africoders.com/" lang="it" title="Italiano">Italiano</a></li>'
        . '<li><a href="https://ja.africoders.com/" lang="ja" title="日本語">日本語</a></li>'
        . '<li><a href="https://ko.africoders.com/" lang="ko" title="한국어">한국어</a></li>'
        . '<li><a href="https://lt.africoders.com/" lang="lt" title="Lietuvių">Lietuvių</a></li>'
        . '<li><a href="https://lv.africoders.com/" lang="lv" title="Latviešu">Latviešu</a></li>'
        . '<li><a href="https://ms.africoders.com/" lang="ms" title="Bahasa Melayu">Bahasa Melayu</a></li>'
        . '<li><a href="https://nb.africoders.com/" lang="nb" title="Norsk Bokmål">Norsk Bokmål</a></li>'
        . '<li><a href="https://nl.africoders.com/" lang="nl" title="Nederlands">Nederlands</a></li>'
        . '<li><a href="https://pl.africoders.com/" lang="pl" title="Polski">Polski</a></li>'
        . '<li><a href="https://pt.africoders.com/" lang="pt" title="Português">Português</a></li>'
        . '<li><a href="https://ro.africoders.com/" lang="ro" title="Română">Română</a></li>'
        . '<li><a href="https://ru.africoders.com/" lang="ru" title="Русский">Русский</a></li>'
        . '<li><a href="https://sk.africoders.com/" lang="sk" title="Slovenčina">Slovenčina</a></li>'
        . '<li><a href="https://sl.africoders.com/" lang="sl" title="Slovenščina">Slovenščina</a></li>'
        . '<li><a href="https://sq.africoders.com/" lang="sq" title="Shqip">Shqip</a></li>'
        . '<li><a href="https://sr.africoders.com/" lang="sr" title="Српски">Српски</a></li>'
        . '<li><a href="https://sv.africoders.com/" lang="sv" title="Svenska">Svenska</a></li>'
        . '<li><a href="https://th.africoders.com/" lang="th" title="ไทย">ไทย</a></li>'
        . '<li><a href="https://tl.africoders.com/" lang="tl" title="Tagalog">Tagalog</a></li>'
        . '<li><a href="https://tr.africoders.com/" lang="tr" title="Türkçe">Türkçe</a></li>'
        . '<li><a href="https://uk.africoders.com/" lang="uk" title="Українська">Українська</a></li>'
        . '<li><a href="https://ur.africoders.com/" lang="ur" title="اردو"><bdi dir="rtl">اردو</bdi></a></li>'
        . '<li><a href="https://vi.africoders.com/" lang="vi" title="Tiếng Việt">Tiếng Việt</a></li>'
        . '<li><a href="https://zh.africoders.com/" lang="zh" title="中文">中文</a></li>'
        . '<li><a href="https://zt.africoders.com/" lang="zt" title="中文繁體">中文繁體</a></li>'
        . '</ul>'
        . '</div>';

    $result = str_replace('<lang></lang>', $replace, $html);
    return $result;
}


function convertCustomBbcodeToHtml($string)
{
    // Define the regex pattern to match the specific BBCode tag
    $pattern = '/\[upl-image-preview url=([^\]]+)\]/';

    // Callback function to replace BBCode with HTML
    $callback = function ($matches) {
        $url = $matches[1];
        return '<img src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" title="" alt="">';
    };

    // Replace BBCode with HTML in the string
    $result = preg_replace_callback($pattern, $callback, $string);


    return $result;
}

function formatContentoutput($html) {
    $html = str_replace("&quot;","&",$html);

    return html_entity_decode($html);
}

if(!function_exists("getTranslatableLocales")) {
    function getTranslatableLocales($key="locales")
    {
        $localeData = require __DIR__."/Settings.php";
        if($key=="microsoft") {return $localeData[$key];}

        $locales = array_keys($localeData[$key]);
        array_unique($locales);
        return $locales;
    }
}

if(!function_exists("setCurrentTranslationLocale")) {
    function setCurrentTranslationLocale($locale)
    {
        $expiration = time() + (7 * 24 * 60 * 60);
        // Set the cookie with the setcookie function
        setcookie('translation_locale', $locale, $expiration, '/');
    }
}


if(!function_exists("getCurrentTranslationLocale")) {
    function getCurrentTranslationLocale()
    {
        if (isset($_COOKIE['translation_locale'])) {
            $cookie_value = $_COOKIE['translation_locale'];
        } else {
            $cookie_value = "en";
        }
        return $cookie_value;
    }
}

if(!function_exists("getDefaultLocale")) {
    function getDefaultLocale()
    {
        $translator = resolve('translator');
        return $translator->getLocale();
    }
}

if(!function_exists('setupDetectedLocale')) {
    function setupDetectedLocale()
    {
        $locales = resolve(LocaleManager::class);

        $language = getDetectedLocale();
        $locales->setLocale($language);
        return $locales;
    }
}

if(!function_exists("getDetectedLocale")) {
    function getDetectedLocale()
    {
        if(!isset($_SERVER['HTTP_HOST'])) {return getDefaultLocale();}

        $host = $_SERVER['HTTP_HOST'];

        $parts = explode('.', $host);

        // Check if there are enough parts to have a subdomain
        if (count($parts) >= 3) {
            // The subdomain is the first part
            $subdomain = $parts[0];
        } else {
            // No subdomain present - so use default
            $subdomain = getDefaultLocale();
        }

        return $subdomain;
    }
}


if(!function_exists("getDetectedSubdomain")) {
    function getDetectedSubdomain()
    {
        if(!isset($_SERVER['HTTP_HOST'])) {return "";}

        $host = $_SERVER['HTTP_HOST'];

        $parts = explode('.', $host);

        // Check if there are enough parts to have a subdomain
        if (count($parts) >= 3) {
            // The subdomain is the first part
            $subdomain = $parts[0];
        } else {
            // No subdomain present - so use default
            $subdomain = "";
        }

        return $subdomain;
    }
}


if(!function_exists("isArrayEmptyValues")) {
    /*
     * $array1 = ["key1" => "", "key2" => 0, "key3" => null];
$array2 = ["key1" => "", "key2" => "not empty", "key3" => null];

var_dump(isArrayEmptyValues($array1)); // bool(true)
var_dump(isArrayEmptyValues($array2)); // bool(false)
     */
    function isArrayEmptyValues($array)
    {
        if(!is_array($array)) {return true;}
        foreach ($array as $value) {
            if (!empty($value)) {
                return false;
            }
        }
        return true;
    }
}

if(!function_exists("refactorTranslationTable")) {
    function refactorTranslationTable($tableName,$direction) {
        $schema = resolve(Builder::class);

        if($direction=="up") {
            //update
            if ($schema->hasTable($tableName)) {
                $schema->table($tableName, function (Blueprint $table) use ($schema, $tableName) {
                    if (!$schema->hasColumn($tableName, '_hash')) {
                        $table->addColumn('string', '_hash', ['length' => 100]);
                    }
                    if (!$schema->hasColumn($tableName, '_entity')) {
                        $table->addColumn('string', '_entity', ['length' => 100]);
                    }
                    if (!$schema->hasColumn($tableName, '_locale')) {
                        $table->addColumn('string', '_locale', ['length' => 100, 'default' => 'en']);
                    }
                    if (!$schema->hasColumn($tableName, '_outdated')) {
                        $table->addColumn('integer', '_outdated', ['default' => 0, 'index' => true]);
                    }
                    if (!$schema->hasColumn($tableName, '_failed')) {
                        $table->addColumn('integer', '_failed', ['default' => 0, 'index' => true]);
                    }
                    if (!$schema->hasColumn($tableName, '_pointer')) {
                        $table->addColumn('integer', '_pointer', ['default' => 0]);
                    }
                    if (!$schema->hasColumn($tableName, '_translated')) {
                        $table->addColumn('integer', '_translated', ['default' => 0, 'index' => true]);
                    }

                    $locales = getTranslatableLocales();

                    foreach ($locales as $locale) {
                        $columnName = "sub_{$locale}";
                        if (!$schema->hasColumn($tableName, $columnName)) {
                            $table->addColumn('text', $columnName, ['default' => null]);
                        }
                    }
                });
            }
        } else {
            //down
            if ($schema->hasTable($tableName)) {
                $schema->table($tableName, function (Blueprint $table) {
                    return;
                    //$table->dropColumn('countryCode');
                    //$table->removeColumn('countryCode');
                });
            }
        }
    }
}


if(!function_exists("logInfo")) {
    function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'dhtml-general-dump.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }
}
