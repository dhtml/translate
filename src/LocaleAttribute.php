<?php

namespace Dhtml\Translate;

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;

/**
 * @property string $hash
 * @property string $source
 * @property string $locale
 * @property string $translation
 */
class LocaleAttribute extends AbstractModel
{
    use ScopeVisibilityTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'translate_attributes';

    //mass assign all except
    protected $guarded = ['id'];

    protected $dates = ['created_at', 'updated_at'];

}
