<?php

namespace Dhtml\Translate\Console;

use Dhtml\Translate\Translate;
use Dhtml\Translate\TranslateCache;
use Flarum\Console\AbstractCommand;
use Psr\Log\LoggerInterface;

class TranslatorClear extends AbstractCommand
{

    /**
     * @var mixed|LoggerInterface
     */
    private $logger;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->logger = resolve(LoggerInterface::class);
    }

    protected function configure()
    {
        $this
            ->setName('translate:clear')
            ->setDescription('Translation clear data');
    }

    protected function fire()
    {
        $this->showInfo("Clearing translation data");
        Translate::truncate();
        TranslateCache::truncate();
    }

    public function showInfo($content)
    {
        $this->info($content);
    }

}
