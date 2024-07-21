<?php

namespace Dhtml\Translate\Console;

use Dhtml\Translate\Services\MediaCacheService;
use Flarum\Console\AbstractCommand;
use Psr\Log\LoggerInterface;

class PublishMediaPreview extends AbstractCommand
{
    public function __construct()
    {
        parent::__construct();

        $this->mediaCacheService = new MediaCacheService();
    }

    protected function configure()
    {
        $this
            ->setName('media:preview:cache')
            ->setDescription('Media Preview Cache');
    }

    protected function fire()
    {
        $this->info("Execute Media Preview Cache");
        $this->mediaCacheService->build();
    }
}
