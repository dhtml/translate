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
        if ($schema->hasTable('translate')) {
            return;
        }

        $schema->create('translate', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hash', 100)->index();
            $table->string('entity', 100)->index();
            $table->string('locale', 100)->index()->nullable()->default("en");
            $table->longText('original');
            $table->integer('outdated')->default(0)->index();
            $table->integer('failed')->nullable()->default(0)->index();
            $table->integer('pointer')->default(0);
            $table->integer('translated')->default(0)->index();
            $table->timestamps();

            // Adding locale-specific columns
            $locales = getTranslatableLocales();

            foreach ($locales as $locale) {
                $table->longText("sub_{$locale}")->nullable();
            }
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('translate');
    },
];
