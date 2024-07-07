<?php

namespace Dhtml\Translate\Console;

use Dhtml\Translate\Services\BatchTranslatorService;
use Flarum\Console\AbstractCommand;
use Flarum\Foundation\Paths;
use Psr\Log\LoggerInterface;

class Translate extends AbstractCommand
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
            ->setName('translate')
            ->setDescription('Translation in batches');
    }

    protected function fire()
    {
        $this->info("Execute language translation");
        $this->batchTranslatorService->start();
    }


    public function showInfo($content)
    {
        $this->info($content);
    }

}
