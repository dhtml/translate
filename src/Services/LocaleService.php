<?php

namespace Dhtml\Translate\Services;

use Flarum\Settings\SettingsRepositoryInterface;

class LocaleService
{
    public function __construct()
    {
        $this->settings = resolve(SettingsRepositoryInterface::class);
        //get complete list there
        $this->locales = getTranslatableLocales();
    }
    public function locales()
    {
        return $this->locales;
    }

    public function getCurrentLocale() {
        //$translator = resolve('translator');
        //return $translator->getLocale();
        return getCurrentTranslationLocale();
    }

    public function getResolvedLocale() {
        return getCurrentTranslationLocale();
    }

    public function selectEnabled()
    {
        static $currentLocales = null;
        if(!$currentLocales) {
            foreach ($this->locales as $locale) {
              if($this->settings->get('dhtml-translate.'. $locale . '.enabled')) {
                  $currentLocales[] = $locale;
              }
            }
        }

        //return ["ar","az","bg","bn","ca","cs","da","de","el","en","eo","es","et","fa","fi","fr","ga","he","hi","hu","id","it","ja","ko","lt","lv","ms","nb","nl","pl","pt","ro","ru","sk","sl","sq","sr","sv","th","tl","tr","uk","ur","vi","zh","zt"];
        //return ["ar","es","fr"];

        return $currentLocales;
    }

}
