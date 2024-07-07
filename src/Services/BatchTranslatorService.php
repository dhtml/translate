<?php

namespace Dhtml\Translate\Services;

use DateTime;
use Dhtml\Translate\Badge;
use Dhtml\Translate\Discussion;
use Dhtml\Translate\Page;
use Dhtml\Translate\Post;
use Dhtml\Translate\Tag;
use Flarum\Foundation\Paths;

class BatchTranslatorService
{
    protected $stackEmpty = false;
    protected $failed = false;
    protected TranslatorService $translatorService;
    protected $translationDriver; //e.g. libre
    protected $maxRateLimit;

    protected $rate = 0;

    protected $cliMode = false;

    protected $restingMinutes = 16; //minutes to rest when API is hot to cool down

    protected $fmt = 'm-d-Y H:ia';

    public function __construct()
    {
        $this->translatorService = new TranslatorService();
        $this->translationEngine = $this->translatorService->getTranslationEngine();


        $this->maxRateLimit = $this->translationEngine->maxRateLimit;
        $this->translationDriver = $this->translationEngine->name;

        $this->localeService = new LocaleService();

        $this->locales = $this->localeService->selectEnabled();

        $this->currentLocale = $this->localeService->getCurrentLocale();

        $this->settingsService = new SettingsService();
    }

    public function start()
    {
        if ($this->settingsService->isTranslatorServiceActive()) {
            $this->settingsService->showLastTranslatorActivity();
            return;
        }

        $this->startedTranslation();

        while (true) {
            $this->translate();

            if ($this->stackEmpty) {
                $this->finishedTranslation();
                break;
            }

            if ($this->failed) {
                $this->pauseTranslation();
            }
        }
    }

    protected function startedTranslation()
    {
        $startTime = new DateTime();
        $formattedStartTime = $startTime->format($this->fmt);
        $this->showInfo("We started @ $formattedStartTime");

        if ($this->settingsService->isLibrePaused()) {
            $this->showInfo("...service will soon resume...");
            while ($this->settingsService->isLibrePaused()) {
                $this->settingsService->keepAlive();
                sleep(40);
            }
        }
    }

    public function showInfo($string)
    {
        echo "$string\n";
    }

    public function translateSKipped($tag = null) {
        if($tag=="post") {
            $items = Post::where('type', 'comment')->where('_translated',1)->orderBy('id', "asc")->get();
            return $this->sendForSkippedTranslation("post", $items);
        }
    }

    protected function translate($tag = null)
    {
        if (!$tag) {
            if (!$this->translateBadges()) {
                return false;
            }
            if (!$this->translateTags()) {
                return false;
            }
            if (!$this->translatePages()) {
                return false;
            }
            if (!$this->translateDiscussions()) {
                return false;
            }
            if (!$this->translatePosts()) {
                return false;
            }
        } else {
            switch ($tag) {
                case "posts":
                    if (!$this->translatePosts("desc")) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    protected function translateBadges()
    {
        $items = Badge::get();
        return $this->sendForTranslation("badge", $items);
    }

    protected function sendForTranslation($itemName, $items)
    {
        // Loop over each badge and update the name column
        foreach ($items as $item) {
            $hash = EntityService::generateHash($itemName, $item);

            if ($hash != $item->_hash) {
                $item->_hash = $hash;
                $item->_pointer = 0;
                $item->_translated = 0;
                $item->_outdated = 0;
                $item->save();
            }

            $tdata = EntityService::toArray($itemName, $item);

            if (!$this->translateEntity($itemName, $item, $tdata)) {
                return false;
            }
        }

        return true;
    }

    protected function sendForSkippedTranslation($itemName, $items) {
        // Loop over each badge and update the name column
        foreach ($items as $item) {
            $tdata = EntityService::toArray($itemName, $item);

            if (!$this->translateSkippedEntity($itemName, $item, $tdata)) {
                return false;
            }
        }

        return true;
    }

    protected function translateSkippedEntity($itemName, $item, $data)
    {
        $source_language = $item->_locale;

        for ($i = 0; $i < count($this->locales); $i++) {
            $_locale = $this->locales[$i];

            //translate per locale
            $tdata = $data;
            foreach ($tdata as $key => &$value) {
                $current = @json_decode($item->{"sub_".$_locale},true);

                //skip non-empty values
                if(!isArrayEmptyValues($current)) {continue;}

                //process the empty value here
                echo "==> $itemName-{$item->id}::{$i}...sub_{$_locale}\n";

                //attempt to translate the skipped locale
                $this->settingsService->keepAlive();
                if ($this->settingsService->isLibrePaused()) {
                    $this->showInfo("...service paused...");
                    $this->failed = 1;
                    return false;
                }
                //attempt to translate
                $response = $this->translatorService->translateHTML($value, $_locale, $source_language);

                if ($response->error_level == 1) {
                    //the translator is never going to be able to do this
                    echo("...skipped $_locale...");
                    $value = ""; //just legover this one
                } else if ($response->error_level == 2) {
                    //the translator has encountered a 500 error
                    echo("...{$response->error_log}...");
                    $this->failed = 1;
                    //$item->failed = 1; //mark failure
                    //$item->save();
                    return false;
                } else {
                    //store the value
                    $value = $response->translation;
                }
            }
            //end of translation for a particular locale
        }

        //finished translating this item (iterated through all supported languages)

        return true;
    }


    protected function translateEntity($itemName, $item, $data)
    {
        $source_language = $item->_locale;

        echo "==> Translating $itemName-{$item->id}";
        echo "...";


        for ($i = $item->_pointer; $i < count($this->locales); $i++) {
            $_locale = $this->locales[$i];

            if (!$this->translatorService->isLocaleSupported($_locale)) {
                echo "...$_locale not supported...";
                continue;
            }

            echo "...$_locale...";

            //translate per locale
            $tdata = $data;
            foreach ($tdata as $key => &$value) {
                $this->settingsService->keepAlive();
                if ($this->settingsService->isLibrePaused()) {
                    $this->showInfo("...service paused...");
                    $this->failed = 1;
                    return false;
                }
                //attempt to translate
                $response = $this->translatorService->translateHTML($value, $_locale, $source_language);

                if ($response->error_level == 1) {
                    //the translator is never going to be able to do this
                    echo("...skipped $_locale...");
                    $value = ""; //just legover this one
                } else if ($response->error_level == 2) {
                    //the translator has encountered a 500 error
                    echo("...{$response->error_log}...");
                    $this->failed = 1;
                    //$item->failed = 1; //mark failure
                    //$item->save();
                    return false;
                } else {
                    //store the value
                    $value = $response->translation;
                }

                /*
                if (empty($response->translation)) {
                    if ($this->translationEngine->name == 'microsoft') {
                        $this->showInfo("missing $itemName-{$item->id} for $_locale");
                        //$item->failed = 1; //mark failure
                        //$item->save();
                    } else {
                        $this->showInfo("Failed to translate $itemName-{$item->id} to $_locale");
                        $this->failed = 1;
                        //$item->failed = 1; //mark failure
                        //$item->save();
                        return false;
                    }
                }
                */
            }

            //save this translation
            $item->{"sub_{$_locale}"} = json_encode($tdata);
            $item->_pointer = $i + 1; //mark pointer
            $item->save();
            //end of translation for a particular locale
        }

        //finished translating this item (iterated through all supported languages)
        $item->_outdated = 0;
        $this->_failed = 0;
        $item->_translated = 1;
        $item->save();
        echo "done\n";

        return true;
    }

    protected function translateTags()
    {
        $items = Tag::get();
        return $this->sendForTranslation("tag", $items);
    }

    protected function translatePages()
    {
        $items = Page::get();
        return $this->sendForTranslation("page", $items);
    }

    protected function translateDiscussions()
    {
        $items = Discussion::get();
        return $this->sendForTranslation("discussion", $items);
    }

    protected function translatePosts($dir = "asc")
    {
        $items = Post::where('type', 'comment')->orderBy('id', $dir)->get();
        return $this->sendForTranslation("post", $items);
    }

    protected function finishedTranslation()
    {
        $startTime = new DateTime();
        $formattedStartTime = $startTime->format($this->fmt);
        $this->showInfo("We finished @ $formattedStartTime");
    }

    protected function pauseTranslation()
    {
        $this->settingsService->pauseLibreAPI();

        $setting = $this->settingsService->get("pauseLibreTranslate");
        $duration = $setting['duration'];
        $from = $setting['from'];
        $to = $setting['to'];

        $this->showInfo("Paused @ $from for $duration minutes, resume @ $to");

        while ($this->settingsService->isLibrePaused()) {
            $this->settingsService->keepAlive();
            sleep(40);
        }
    }

    public function startWithParam($param)
    {

        $this->startedTranslation();

        $this->cliMode = true;
        while (true) {
            $this->translate($param); // Call the translate function


            if ($this->stackEmpty) {
                $this->finishedTranslation();
                break;
            }

            if ($this->failed) {
                $this->pauseTranslation();
            }
        }
    }

    public function startWithSkipped($param)
    {
        $this->startedTranslation();
        $this->translateSKipped($param); // Call the translate function
    }

    public function startUntilEmpty()
    {
        $this->startedTranslation();

        $this->cliMode = true;
        while (true) {
            $this->translate(); // Call the translate function

            if ($this->stackEmpty) {
                $this->finishedTranslation();
                break;
            }

            if ($this->failed) {
                $this->pauseTranslation();
            }
        }
    }

    public function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'batch-translator-service.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }

}
