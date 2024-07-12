<?php

namespace Dhtml\Translate\Console;

use Dhtml\Translate\Services\LanguageService;
use Dhtml\Translate\Services\StringTranslationService;
use Flarum\Console\AbstractCommand;
use Psr\Log\LoggerInterface;

class TranslateStringsStore extends AbstractCommand
{

    /**
     * @var mixed|LoggerInterface
     */
    private $logger;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->logger = resolve(LoggerInterface::class);
        $this->languageService = new LanguageService();
    }

    protected function configure()
    {
        $this
            ->setName('translate:strings:store')
            ->setDescription('Store locales');
    }

    protected function fire()
    {
        $this->info("Store the strings inside language files");

        $this->languageService->start();
    }


    public function showInfo($content)
    {
        $this->info($content);
    }

}
