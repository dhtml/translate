<?php

namespace Dhtml\Translate\Providers;


use Flarum\Foundation\AbstractServiceProvider;

class LocaleServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->extend('config', function ($container) {
            $container->set('session.lifetime', 2628000);
            return $container;
        });
    }
}
