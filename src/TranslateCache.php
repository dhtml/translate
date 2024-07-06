<?php

namespace Dhtml\Translate;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;

/**
 * @property string $hash
 * @property string $source
 * @property string $locale
 * @property string $translation
 */
class TranslateCache extends AbstractModel
{
    use ScopeVisibilityTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'translate_cache';

    //mass assign all except
    protected $guarded = ['id'];

    protected $dates = ['created_at', 'updated_at'];

}
