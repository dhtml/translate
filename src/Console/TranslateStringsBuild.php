<?php

namespace Dhtml\Translate\Console;

use Dhtml\Translate\Services\BatchTranslatorService;
use Dhtml\Translate\Services\StringTranslationService;
use Flarum\Console\AbstractCommand;
use Psr\Log\LoggerInterface;

class TranslateStringsBuild extends AbstractCommand
{

    /**
     * @var mixed|LoggerInterface
     */
    private $logger;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->logger = resolve(LoggerInterface::class);
        $this->stringTranslatorService = new StringTranslationService();
    }

    protected function configure()
    {
        $this
            ->setName('translate:strings:build')
            ->setDescription('Build translatable strings');
    }

    protected function fire()
    {
        $this->info("Execute language translation all strings");
        $this->stringTranslatorService->start();
    }


    public function showInfo($content)
    {
        $this->info($content);
    }

}
