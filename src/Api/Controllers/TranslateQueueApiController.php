<?php

namespace Dhtml\Translate\Api\Controllers;


use Dhtml\Translate\Api\Serializer\TranslateQueueSerializer;
use Dhtml\Translate\Services\BatchTranslatorService;
use Flarum\Api\Controller\AbstractShowController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;


class TranslateQueueApiController extends AbstractShowController
{
    public $serializer = TranslateQueueSerializer::class;

    public function __construct()
    {
        $this->batchTranslatorService = new BatchTranslatorService();
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        ob_start();
        $this->batchTranslatorService->start();
        @ob_end_clean();

        return (object) [
            'id' => "1",
            'queue' => "1",
        ];
    }
}
