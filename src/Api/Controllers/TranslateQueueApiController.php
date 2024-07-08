<?php

namespace Dhtml\Translate\Api\Controllers;


use Dhtml\Translate\Api\Serializer\TranslateQueueSerializer;
use Dhtml\Translate\Services\BatchTranslatorService;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;


class TranslateQueueApiController extends AbstractShowController
{
    public $serializer = TranslateQueueSerializer::class;

    public function __construct()
    {
        $this->settings = resolve(SettingsRepositoryInterface::class);

        $this->batchTranslatorService = new BatchTranslatorService();

        $this->cronLess = $this->settings->get('dhtml-translate.cronLess');
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        if($this->cronLess) {
            ob_start();
            $this->batchTranslatorService->start();
            @ob_end_clean();

            return (object)[
                'id' => "1",
                'queue' => "1",
            ];
        } else {
            return (object)[
                'id' => "1",
                'queue' => "0",
            ];
        }
    }
}
