<?php

namespace Dhtml\Translate\Listeners;

use Flarum\Foundation\Paths;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Flarum\Post\Event\Posted;
use Flarum\Post\Event\Saving;
use Flarum\Discussion\Event\Started;


class ForumListener
{
    protected $settings;
    protected $events;

    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events)
    {
        $this->settings = $settings;
        $this->events = $events;
    }

    public function postWasPosted(Posted $event): void
    {
        $post = $event->post;

        $this->triggerPostEvent($post);
    }

    public function postWasSaved(Saving $event): void
    {
        $post = $event->post;
        $this->triggerPostEvent($post);
    }

    public function discussionWasStarted(Started $event)
    {
        $discussion = $event->discussion;

        $discussion->_locale = getDetectedLocale();
        $discussion->save();

        //$this->logInfo("Start discussion");
        //$this->logInfo("started discussion " . json_encode($discussion->toArray()));
    }

    public function logInfo($content)
    {
        $paths = resolve(Paths::class);
        $logPath = $paths->storage . (DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'dhtml-translator-forum-listener.log');
        $content = var_export($content, true);
        file_put_contents($logPath, $content, FILE_APPEND);
    }

    protected function triggerPostEvent($post)
    {
        $post->_locale = getDetectedLocale();
        $post->media_html = null;
        if($post->_translated==1) {
            $post->_outdated = 1;
        }
        $post->save();

        //$event->post->content
        //$this->logInfo("trigger stuffs" . json_encode($post->toArray()));
    }

}
