<?php

namespace Dhtml\Translate\Console;

use Illuminate\Console\Scheduling\Event;
use Flarum\Foundation\Paths;

class BatchTranslatorSchedule
{

    public function __invoke(Event $event)
    {
        $event
            ->everyMinute()
            ->withoutOverlapping();

            $event->onOneServer();

            $paths = resolve(Paths::class);
            $event->appendOutputTo($paths->storage.(DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'translator-schedule.log'));
    }
}
