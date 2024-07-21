<?php

namespace Dhtml\Translate\Services;

use Dhtml\Translate\Post;
use Flarum\Settings\SettingsRepositoryInterface;

class MediaCacheService
{
    /**
     * @var SettingsRepositoryInterface|mixed
     */
    protected $settings;

    public function __construct()
    {
        $this->settings = resolve(SettingsRepositoryInterface::class);
    }

    public function build() {
        $results = [];

        Post::whereNotNull('media_html')
            ->where('media_html', '!=', '')
            ->chunk(100, function ($posts) use(&$results) {
                foreach ($posts as $post) {
                    $results["$post->id"] = $post->media_html;
                }
            });

        $this->settings->set('dhtml-translate.mediaPreviewCache', json_encode($results));
    }

    public function saveSystemSettings($settings) {
    }


}
