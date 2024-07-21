<?php

namespace Dhtml\Translate\Console;

use Flarum\Foundation\Paths;
use Illuminate\Console\Scheduling\Event;

class PublishMediaPreviewSchedule
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
