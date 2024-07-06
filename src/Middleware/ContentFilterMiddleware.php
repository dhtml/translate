<?php

namespace Dhtml\Translate\Middleware;

use Dhtml\Translate\Services\ContentFilterService;
use Flarum\Foundation\Paths;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ContentFilterMiddleware implements MiddlewareInterface
{
    protected $contentFilterService;

    public function __construct()
    {
        $this->contentFilterService = new ContentFilterService();
    }
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // Process HTML and JSON responses
        $contentType = $response->getHeaderLine('Content-Type');

        if (strpos($contentType, 'text/html') !== false || strpos($contentType, 'application/json') !== false || strpos($contentType, 'application/vnd.api+json') !== false) {
            $body = (string) $response->getBody();

            $body = $this->contentFilterService->modifyContent($body, $contentType);

            // Convert modified content to StreamInterface
            $stream = Utils::streamFor($body);

            // Create a new response with the modified body
            $response = $response->withBody($stream);
        }

        return $response;
    }
}
