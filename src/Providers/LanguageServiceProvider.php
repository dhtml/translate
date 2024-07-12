<?php

namespace Dhtml\Translate\Providers;

use Dhtml\Translate\Services\AttributesTranslationService;
use Flarum\Foundation\AbstractServiceProvider;

class LanguageServiceProvider extends AbstractServiceProvider
{

    public function __construct()
    {
        $this->attributesTranslatorService = new AttributesTranslationService();
    }

    public function register()
    {
        $this->attributesTranslatorService->translateAttributes();
    }
}
