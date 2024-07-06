<?php

namespace Dhtml\Translate\Controllers;

use Dhtml\Translate\Services\BatchTranslatorService;
use Dhtml\Translate\Services\SettingsService;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\View\Factory;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class BaseController implements RequestHandlerInterface
{
    /**
     * @var Factory
     */
    protected $view;

    public function __construct(Factory $view)
    {
        $this->view = $view;
        $this->settingsService = new SettingsService();
    }

    public function handle(Request $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $key = $queryParams['key'] ?? null;
        $value = $queryParams['value'] ?? null;


        ob_start();
        echo "<pre>";
        if($key=="test1") {
          $response = $this->settingsService->pauseLibreAPI();
            if($response) {
                $this->write("Libre API has been paused successfully");
            } else {
                $this->write("Libre API is already in pause mode");
            }

        } else if($key=="test2") {

           $paused =  $this->settingsService->isLibrePaused();

           if($paused) {
               $this->write("Libre API is currently paused");
           } else {
               $this->write("Libre API is currently available");
           }
        } else if($key=="test3") {
            $this->settingsService->remove("pauseLibreTranslate");
            $this->write("Libre timeout clear");
        } else {
            $this->write("No test case");
        }

        $settings = $this->settingsService->getSystemSettings();

        echo var_export($settings,true);

            $content = ob_get_contents();
        @ob_clean();

        $html = nl2br($content);

        return new HtmlResponse($html);
    }

    public function write($string) {
        echo "$string\n";
    }
}
