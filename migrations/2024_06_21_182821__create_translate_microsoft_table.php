<?php

/*
 * This file is part of fof/pages.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

require_once __DIR__."/../src/helpers.php";

return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('translate_microsoft')) {
            return;
        }


        $schema->create('translate_microsoft', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hash', 100)->unique();
            $table->string('locale', 10)->nullable()->default("en");
            $table->longText('source');

            $locales = getTranslatableLocales("microsoft");

            foreach ($locales as $locale) {
                $locale = strtolower($locale);
                $table->longText("sub_{$locale}")->nullable();
            }

            $table->timestamps();
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('translate_microsoft');
    },
];
