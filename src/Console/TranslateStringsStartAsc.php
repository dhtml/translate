<?php

namespace Dhtml\Translate\Console;

use Dhtml\Translate\Services\BatchTranslatorService;
use Flarum\Console\AbstractCommand;
use Psr\Log\LoggerInterface;

class TranslateStringsStartAsc extends AbstractCommand
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
            ->setName('translate:strings:start:asc')
            ->setDescription('Build translatable start');
    }

    protected function fire()
    {
        $this->info("Execute language translation all strings");
        $this->batchTranslatorService->startWithParam("strings","asc");
    }


    public function showInfo($content)
    {
        $this->info($content);
    }

}
