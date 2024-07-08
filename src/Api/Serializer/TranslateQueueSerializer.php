<?php

/*
 * This file is part of fof/pages.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dhtml\Translate\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;

class TranslateQueueSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'translate:queue';

    protected function getDefaultAttributes($translate)
    {
        $attributes = [
            "queue"=>$translate->queue,
        ];
        return $attributes;
    }
}
