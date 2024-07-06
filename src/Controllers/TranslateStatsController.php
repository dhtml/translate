<?php

namespace Dhtml\Translate\Controllers;

use Dhtml\Translate\Services\StatsService;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\View\Factory;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;

class TranslateStatsController implements RequestHandlerInterface
{
    /**
     * @var Factory
     */
    protected $view;

    public function __construct(Factory $view)
    {
        $this->view = $view;
        $this->statsService = new StatsService();

        $this->settings = resolve(SettingsRepositoryInterface::class);
        $this->cronKey = $this->settings->get('dhtml-translate.cronKey');
    }

    public function handle(Request $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $key = $queryParams['key'] ?? null;

        if($key != $this->cronKey) {
            $html = "The translation service is not currently available";
            return new HtmlResponse($html);
        }

        $html = $this->statsService->display();

        return new HtmlResponse($html);
    }
}
