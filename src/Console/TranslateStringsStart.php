<?php

namespace Dhtml\Translate\Console;

use Dhtml\Translate\Services\BatchTranslatorService;
use Dhtml\Translate\Services\StringTranslationService;
use Flarum\Console\AbstractCommand;
use Psr\Log\LoggerInterface;

class TranslateStringsStart extends AbstractCommand
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
            ->setName('translate:strings:start')
            ->setDescription('Build translatable start');
    }

    protected function fire()
    {
        $this->info("Execute language translation all strings");
        $this->batchTranslatorService->startWithParam("strings");
    }


    public function showInfo($content)
    {
        $this->info($content);
    }

}
