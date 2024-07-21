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
        if ($schema->hasTable('posts')) {
            if ($schema->hasColumn('posts', 'media_html')) {
                return;
            }

            $schema->table('posts', function (Blueprint $table) {
                $table->string('media_html')->nullable();
            });

        }
    },
    'down' => function (Builder $schema) {
        $schema->table('posts', function (Blueprint $table) {
        });
    }

];
