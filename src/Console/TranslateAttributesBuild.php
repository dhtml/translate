<?php

namespace Dhtml\Translate\Console;

use Dhtml\Translate\Services\AttributesTranslationService;
use Flarum\Console\AbstractCommand;
use Psr\Log\LoggerInterface;

class TranslateAttributesBuild extends AbstractCommand
{

    /**
     * @var mixed|LoggerInterface
     */
    private $logger;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->logger = resolve(LoggerInterface::class);
        $this->attributesTranslatorService = new AttributesTranslationService();
    }

    protected function configure()
    {
        $this
            ->setName('translate:attributes:build')
            ->setDescription('Build translatable attributes');
    }

    protected function fire()
    {
        $this->info("Execute language translation all attributes");
        $this->attributesTranslatorService->start();
    }


    public function showInfo($content)
    {
        $this->info($content);
    }

}
