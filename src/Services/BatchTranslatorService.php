<?php

namespace Dhtml\Translate\Services;

use DateTime;
use Dhtml\Translate\Badge;
use Dhtml\Translate\Discussion;
use Dhtml\Translate\Page;
use Dhtml\Translate\Post;
use Dhtml\Translate\Tag;

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

    public function __construct()
    {
        $this->translatorService = new TranslatorService();
        $this->translationEngine = $this->translatorService->getTranslationEngine();


        $this->maxRateLimit = $this->translationEngine->maxRateLimit;
        $this->translationDriver = $this->translationEngine->name;

        $this->localeService = new LocaleService();

        $this->locales = $this->localeService->selectEnabled();

        $this->currentLocale = $this->localeService->getCurrentLocale();
    }

    public function start()
    {
        set_time_limit(60 * 15); // Ensure the script does not exceed 5 minutes.
        $startTime = time(); // Record the start time
        $maxDuration = 240; // 4 minutes in seconds

        while (true) {
            $this->translate(); // Call the translate function
            break;

            if ($this->stackEmpty || $this->failed) {
                break;
            }

            // Check if 5 minutes have passed
            if ((time() - $startTime) >= $maxDuration) {
                break; // Exit the loop if the maximum duration has been reached
            }
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

                if (is_array($value)) {
                    //do not bother to translate, leave as is
                } else {
                    //attempt to translate
                    $response = $this->translatorService->translateHTML($value, $_locale, $source_language);


                    if (empty($response->translation) || trim($response->translation) == "") {
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
                    } else {
                        $value = $response->translation;
                    }
                }
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

    public function showInfo($string)
    {
        echo "$string\n";
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
        $items = Post::where('type','comment')->orderBy('id', $dir)->get();
        return $this->sendForTranslation("post", $items);
    }

    public function startWithParam($param)
    {
        $fmt = 'm-d-Y H:ia';

        $startTime = new DateTime();
        $formattedBreakTime = $startTime->format($fmt);

        $this->showInfo("We started @ $formattedBreakTime");

        $this->cliMode = true;
        while (true) {
            $this->translate($param); // Call the translate function

            // Check if stack is empty or if there was a failure
            if ($this->stackEmpty || $this->failed) {
                $breakTime = new DateTime();
                $currentTime = $breakTime->format($fmt);
                $breakTime->modify("+{$this->restingMinutes} minutes");
                $formattedBreakTime = $breakTime->format($fmt);

                $message = "Stopped @ $currentTime";
                if ($this->stackEmpty) {
                    $message .= "..stack empty";
                } else if ($this->failed) {
                    $message .= "...api failure";
                }

                $this->showInfo("$message, We rest till $formattedBreakTime");
                sleep(60 * $this->restingMinutes); // Sleep for 30 minutes

                $startTime = new DateTime();
                $formattedBreakTime = $startTime->format($fmt);
                $this->showInfo("We resumed @ $formattedBreakTime");
            }
        }
    }

    public function startUntilEmpty()
    {
        $fmt = 'm-d-Y H:ia';

        $startTime = new DateTime();
        $formattedBreakTime = $startTime->format($fmt);

        $this->showInfo("We started @ $formattedBreakTime");

        $this->cliMode = true;
        while (true) {
            $this->translate(); // Call the translate function

            // Check if stack is empty or if there was a failure
            if ($this->stackEmpty || $this->failed) {
                $breakTime = new DateTime();
                $currentTime = $breakTime->format($fmt);
                $breakTime->modify("+$this->restingMinutes minutes");
                $formattedBreakTime = $breakTime->format($fmt);

                $message = "Stopped @ $currentTime";
                if ($this->stackEmpty) {
                    $message .= "..stack empty";
                } else if ($this->failed) {
                    $message .= "...api failure";
                }

                $this->showInfo("$message, We rest till $formattedBreakTime");
                sleep(60 * $this->restingMinutes); // Sleep for 30 minutes

                $startTime = new DateTime();
                $formattedBreakTime = $startTime->format($fmt);
                $this->showInfo("We resumed @ $formattedBreakTime");
            }
        }
    }


}
