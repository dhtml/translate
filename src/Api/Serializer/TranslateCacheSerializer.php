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

class TranslateCacheSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'translation';

    /**
     * @param TranslateCache $translateCache
     *
     * @return array
     */
    protected function getDefaultAttributes($translateCache)
    {
        $attributes = [
            'from_locale' => $translateCache->from_locale,
            'to_locale' => $translateCache->to_locale,
            'source' => $translateCache->source,
            'translation' => $translateCache->translation,
            'mode' => $translateCache->mode,
        ];
        return $attributes;
    }
}
