<?php

namespace Dhtml\Translate\Console;

use Dhtml\Translate\Services\BatchTranslatorService;
use Flarum\Console\AbstractCommand;
use Psr\Log\LoggerInterface;

class TranslateSkippedPost extends AbstractCommand
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
            ->setName('translate:posts:skipped')
            ->setDescription('Translation all skipped posts');
    }

    protected function fire()
    {
        $this->info("Execute language translation all skipped posts");
        $this->batchTranslatorService->startWithSkipped("post");
    }


    public function showInfo($content)
    {
        $this->info($content);
    }

}
