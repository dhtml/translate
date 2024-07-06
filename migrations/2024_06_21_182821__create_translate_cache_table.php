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
        if ($schema->hasTable('translate_cache')) {
            return;
        }

        $schema->create('translate_cache', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('characters')->default(0);
            $table->integer('chunk_size')->default(0);
            $table->string('hash', 100);
            $table->string('from_locale', 2);
            $table->string('to_locale', 2);
            $table->string('translator', 15);
            $table->longText('source');
            $table->longText('translation');
            $table->timestamps();
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('translate_cache');
    },
];
