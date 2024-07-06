<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

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
