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

return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('translate_strings')) {
            return;
        }

        $schema->create('translate_strings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('_hash', 100)->index();
            $table->string('_locale', 100)->index()->nullable()->default("en");
            $table->longText('original');
            $table->integer('_translatable')->nullable()->default(0)->index();
            $table->integer('_outdated')->default(0)->index();
            $table->integer('_failed')->nullable()->default(0)->index();
            $table->integer('_pointer')->default(0);
            $table->integer('_translated')->default(0)->index();
            $table->timestamps();

            $locales = getTranslatableLocales();

            foreach ($locales as $locale) {
                $columnName = "sub_{$locale}";
                    $table->text("sub_{$locale}")->nullable();
            }
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('translate_strings');
    },
];
