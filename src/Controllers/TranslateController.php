<?php

namespace Dhtml\Translate\Controllers;

use Dhtml\Translate\Services\BatchTranslatorService;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\View\Factory;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class TranslateController implements RequestHandlerInterface
{
    /**
     * @var Factory
     */
    protected $view;

    public function __construct(Factory $view)
    {
        $this->view = $view;
        $this->batchTranslatorService = new BatchTranslatorService();

        $this->settings = resolve(SettingsRepositoryInterface::class);
        $this->cronKey = $this->settings->get('dhtml-translate.cronKey');
    }

    public function handle(Request $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $key = $queryParams['key'] ?? null;

        if($key != $this->cronKey) {
            $html = "The cron service is not currently available";
            return new HtmlResponse($html);
        }


        ob_start();
        $this->batchTranslatorService->start();
        $content = ob_get_contents();
        @ob_clean();

        $content = nl2br($content);

        $html = 'Translation Cron Completed at ' . date("y-m-d h:i:s");
        $html .= "<br>Result:<br>$content";

        return new HtmlResponse($html);
    }
}
