<?php

namespace Dhtml\Translate\Providers;

use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;

class LanguageServiceProvider extends AbstractServiceProvider
{

    public function __construct()
    {
        $this->settings = resolve(SettingsRepositoryInterface::class);
    }

    public function register()
    {
     //$welcomeMessage =  $this->settings->get('welcome_message');
     //$this->settings->set('welcome_message', "Discuss anything from latest technology trends to software development. Find your community and share your voice at Africoders!");
     //$this->settings->set('welcome_message', "Welcome to Africoders " . date('m-d-Y h:i:s'));


        /*
        $this->app->make(Extend\Settings::class)->serializeToForum('welcomeMessage', 'custom-welcome-message', function ($value) {
            return $value ?: 'Welcome to the Forum!';
        });
        */
    }
}
