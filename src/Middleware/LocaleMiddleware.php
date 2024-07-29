<?php

namespace Dhtml\Translate\Middleware;

use Dhtml\Translate\Services\RedirectService;
use Flarum\Locale\LocaleManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class LocaleMiddleware implements Middleware
{

    /**
     * @param LocaleManager $locales
     */
    public function __construct(LocaleManager $locales)
    {
        $this->redirectService = new RedirectService($locales);
    }

    public function process(Request $request, Handler $handler): Response
    {
        return $this->redirectService->process($request, $handler);
    }


}
