<?php

namespace Dhtml\Translate\Services;

use DateTime;
use Flarum\Settings\SettingsRepositoryInterface;

class SettingsService
{
    /**
     * @var SettingsRepositoryInterface|mixed
     */
    protected $settings;

    protected $fmt = 'm-d-Y H:ia';

    public function __construct()
    {
        $this->settings = resolve(SettingsRepositoryInterface::class);
    }

    public function getSystemSettings() {
        return @json_decode($this->settings->get('dhtml-translate.translateSettings'),true);
    }

    public function saveSystemSettings($settings) {
        $this->settings->set('dhtml-translate.translateSettings', json_encode($settings));
    }


    public function get($name) {
        $settings = $this->getSystemSettings();
        return $settings[$name] ?? null;
    }

    public function set($name,$value) {
        $settings = $this->getSystemSettings();
        $settings[$name] = $value;
        $this->saveSystemSettings($settings);
    }


    public function remove($name) {
        $settings = $this->getSystemSettings();
        unset($settings[$name]);
        $this->saveSystemSettings($settings);
    }

    /**
     * This allows the API to pause
     *
     * @return void
     */
    public function pauseLibreAPI()
    {
        if($this->isLibrePaused()) {
            return false;
        }
        $duration = $this->settings->get("dhtml-translate.libreRestTime");

        $startTime = new DateTime();
        $currentTime = $startTime->format($this->fmt); //start

        $_currentTime = time(); // start time in unix
        $_resumeTime = $_currentTime + ($duration * 60); // resume time

        $startTime->modify("+$duration minutes");
        $resumeTime = $startTime->format($this->fmt); //finish


        $this->set("pauseLibreTranslate",[
            "rand" => mt_rand(),
            "duration" =>$duration,
            "from"=> $currentTime,
            "to"=> $resumeTime,
            "start"=> $_currentTime,
            "stop"=> $_resumeTime,
        ]);

        return true;
    }

    public function isLibrePaused() {
        $pauseLibreTranslate = $this->get("pauseLibreTranslate");
        if(!is_array($pauseLibreTranslate)) return false;

        $stop = $pauseLibreTranslate['stop'];
        if($stop>time()) return true; //pause still on

        return false;
    }

    public function showInfo($string)
    {
        echo "$string\n";
    }

    public function keepAlive()
    {
        $startTime = new DateTime();
        $currentTime = $startTime->format($this->fmt); //start

        $data = [
            "currentTime" => $currentTime,
            "timeStamp" => time(),
        ];

        $this->set("alive",$data);
        //$this->showInfo("Keep alive:" . json_encode($data));
    }

    public function isTranslatorServiceActive()
    {
        // Retrieve the 'alive' setting
        $alive = $this->get("alive");

        // Check if 'timeStamp' is set and retrieve its value
        $timeStamp = isset($alive['timeStamp']) ? $alive['timeStamp'] : null;

        // Get the current Unix timestamp
        $currentTime = time();

        $timediff = abs($currentTime - $timeStamp);
        echo "Time different is $timediff\n";

        if ($timeStamp && ($timediff) <= 120) {
            return true;
        }

        return false; // Service was not active at least 2 minutes ago
    }
}
