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
        refactorTranslationTable("tags","up");
    },
    'down' => function (Builder $schema) {
        refactorTranslationTable("tags","down");
    },
];
