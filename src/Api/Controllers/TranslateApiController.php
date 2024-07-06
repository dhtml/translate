<?php

namespace Dhtml\Translate\Api\Controllers;


use Dhtml\Translate\Api\Serializer\TranslateCacheSerializer;
use Dhtml\Translate\Services\TranslatorService;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;


class TranslateApiController extends AbstractShowController
{
    public $serializer = TranslateCacheSerializer::class;

    public function __construct()
    {
        $this->translatorService = new TranslatorService();
        $this->settings = resolve(SettingsRepositoryInterface::class);
        $this->cronKey = $this->settings->get('dhtml-translate.cronKey');
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $source = Arr::get($request->getParsedBody(), 'source');
        $locale = Arr::get($request->getParsedBody(), 'locale');

        $response = $this->translatorService->translateHTML($source,$locale);

        return (object) [
            'id'=> $response->id,
            'from_locale' => $response->from_locale,
            'to_locale' => $response->to_locale,
            'source' => $response->source,
            'translation' => $response->translation,
            'mode' => $response->mode,
        ];
    }
}
