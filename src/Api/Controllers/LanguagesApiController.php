<?php

namespace Dhtml\Translate\Api\Controllers;


use Dhtml\Translate\Api\Serializer\LanguagesSerializer;
use Dhtml\Translate\Api\Serializer\TranslateCacheSerializer;
use Dhtml\Translate\Services\TranslatorService;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;


class LanguagesApiController extends AbstractShowController
{
    public $serializer = LanguagesSerializer::class;

    public function __construct()
    {
        $this->settings = resolve(SettingsRepositoryInterface::class);
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {

        return (object) [
            'id'=> 1,
            //'from_locale' => $response->from_locale,
        ];
    }
}
