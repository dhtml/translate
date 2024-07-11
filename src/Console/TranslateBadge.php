<?php

namespace Dhtml\Translate\Console;

use Dhtml\Translate\Services\BatchTranslatorService;
use Flarum\Console\AbstractCommand;
use Psr\Log\LoggerInterface;

class TranslateBadge extends AbstractCommand
{

    /**
     * @var mixed|LoggerInterface
     */
    private $logger;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->logger = resolve(LoggerInterface::class);
        $this->batchTranslatorService = new BatchTranslatorService();
    }

    protected function configure()
    {
        $this
            ->setName('translate:badges')
            ->setDescription('Translation all badges');
    }

    protected function fire()
    {
        $this->info("Execute language translation all badges");
        $this->batchTranslatorService->startWithParam("badges");
    }


    public function showInfo($content)
    {
        $this->info($content);
    }

}
