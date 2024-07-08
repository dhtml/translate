<?php

use Flarum\Foundation\Paths;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Flarum\Formatter\Formatter;

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

if(!function_exists("getDetectedLocale")) {
    function getDetectedLocale()
    {
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
