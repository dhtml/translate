<?php

namespace Dhtml\Translate;

use Flarum\Post\Event\Posted;
use Flarum\Discussion\Event\Started;

use Dhtml\Translate\Api\Controllers\LanguagesApiController;
use Dhtml\Translate\Api\Controllers\TranslateApiController;
use Dhtml\Translate\Controllers\BaseController;
use Dhtml\Translate\Controllers\TranslateController;
use Dhtml\Translate\Controllers\TranslateStatsController;
use Dhtml\Translate\Middleware\ContentFilterMiddleware;
use Dhtml\Translate\Middleware\LocaleMiddleware;
use Flarum\Extend;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Post\PostValidator;
use Illuminate\Support\Str;

require_once __DIR__."/src/helpers.php";

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Routes('api'))
        ->post('/translate', 'language.translator.index', TranslateApiController::class)
        ->post('/languages', 'languages.index', LanguagesApiController::class),

    (new Extend\Routes('forum'))
        ->get('/cron/translate', 'cron.translate.controller', TranslateController::class)
        ->get('/translate/stage', 'stage.translate.controller', BaseController::class)
        ->get('/translate/stats', 'stats.translate.controller', TranslateStatsController::class),

    (new Extend\Middleware('forum'))
        ->add(ContentFilterMiddleware::class)
        ->add(LocaleMiddleware::class),


    (new Extend\Middleware('api'))
        ->add(ContentFilterMiddleware::class),

    (new Extend\Console())
        ->command(Console\Translate::class),
        //->schedule(Console\Translate::class, Console\BatchTranslatorSchedule::class),

    (new Extend\Console())
        ->command(Console\TranslateAll::class),

    (new Extend\Console())
        ->command(Console\TranslatePost::class),

    (new Extend\Console())
        ->command(Console\TranslatorClear::class),

    (new Extend\Console())
        ->command(Console\TranslateSkippedPost::class),


(new Extend\Event())
        ->listen(Posted::class, [Listeners\ForumListener::class, 'postWasPosted'])
        ->listen(Started::class, [Listeners\ForumListener::class, 'discussionWasStarted']),

    (new Extend\Settings())
        ->default('dhtml-translate.plugin.engine', 'libre')
        ->default('dhtml-translate.af.enabled', false)
        ->default('dhtml-translate.rate-limit', 60)
        ->default('dhtml-translate.post-limit', 65535)
        ->default('dhtml-translate.subdomains', false)
        ->default('dhtml-translate.cronKey', "12345")
        ->default('dhtml-translate.googleApiKey', "1234543456")
        ->default('dhtml-translate.microsoftApiKey', "1234543456")
        ->default('dhtml-translate.libreApiKey', "1234543456")
        ->default('dhtml-translate.libreRestTime', "15")
        ->default('dhtml-translate.translateSettings', "[]")
        ->serializeToForum('dhtmlLanguageMenu', "dhtml-translate.locales",null,"en, ar, zh, fr, de, hi, pt, ru, es"),

    (new Extend\Settings())
        ->default('dhtml-translate.am.enabled', false)
        ->default('dhtml-translate.ar.enabled', false)
        ->default('dhtml-translate.as.enabled', false)
        ->default('dhtml-translate.az.enabled', false)
        ->default('dhtml-translate.ba.enabled', false)
        ->default('dhtml-translate.be.enabled', false)
        ->default('dhtml-translate.bg.enabled', false)
        ->default('dhtml-translate.bn.enabled', false)
        ->default('dhtml-translate.bs.enabled', false)
        ->default('dhtml-translate.ca.enabled', false)
        ->default('dhtml-translate.cs.enabled', false)
        ->default('dhtml-translate.cy.enabled', false)
        ->default('dhtml-translate.da.enabled', false)
        ->default('dhtml-translate.de.enabled', false)
        ->default('dhtml-translate.dv.enabled', false)
        ->default('dhtml-translate.el.enabled', false)
        ->default('dhtml-translate.en.enabled', false)
        ->default('dhtml-translate.eo.enabled', false)
        ->default('dhtml-translate.es.enabled', false)
        ->default('dhtml-translate.et.enabled', false)
        ->default('dhtml-translate.eu.enabled', false)
        ->default('dhtml-translate.fa.enabled', false)
        ->default('dhtml-translate.fi.enabled', false)
        ->default('dhtml-translate.fil.enabled', false)
        ->default('dhtml-translate.fj.enabled', false)
        ->default('dhtml-translate.fo.enabled', false)
        ->default('dhtml-translate.fr.enabled', false)
        ->default('dhtml-translate.ga.enabled', false)
        ->default('dhtml-translate.gl.enabled', false)
        ->default('dhtml-translate.gu.enabled', false),

    (new Extend\Settings())
        ->default('dhtml-translate.ha.enabled', false)
        ->default('dhtml-translate.he.enabled', false)
        ->default('dhtml-translate.hi.enabled', false)
        ->default('dhtml-translate.hr.enabled', false)
        ->default('dhtml-translate.hu.enabled', false)
        ->default('dhtml-translate.hy.enabled', false)
        ->default('dhtml-translate.id.enabled', false)
        ->default('dhtml-translate.ig.enabled', false)
        ->default('dhtml-translate.is.enabled', false)
        ->default('dhtml-translate.it.enabled', false)
        ->default('dhtml-translate.ja.enabled', false)
        ->default('dhtml-translate.ka.enabled', false)
        ->default('dhtml-translate.kk.enabled', false)
        ->default('dhtml-translate.km.enabled', false)
        ->default('dhtml-translate.kn.enabled', false)
        ->default('dhtml-translate.ko.enabled', false)
        ->default('dhtml-translate.ku.enabled', false)
        ->default('dhtml-translate.ky.enabled', false)
        ->default('dhtml-translate.lo.enabled', false)
        ->default('dhtml-translate.lt.enabled', false)
        ->default('dhtml-translate.lv.enabled', false)
        ->default('dhtml-translate.mg.enabled', false)
        ->default('dhtml-translate.mi.enabled', false)
        ->default('dhtml-translate.mk.enabled', false)
        ->default('dhtml-translate.ml.enabled', false)
        ->default('dhtml-translate.mn.enabled', false)
        ->default('dhtml-translate.mr.enabled', false)
        ->default('dhtml-translate.ms.enabled', false)
        ->default('dhtml-translate.mt.enabled', false)
        ->default('dhtml-translate.my.enabled', false)
        ->default('dhtml-translate.ne.enabled', false)
        ->default('dhtml-translate.nb.enabled', false)
        ->default('dhtml-translate.nl.enabled', false)
        ->default('dhtml-translate.no.enabled', false)
        ->default('dhtml-translate.ny.enabled', false)
        ->default('dhtml-translate.or.enabled', false)
        ->default('dhtml-translate.pa.enabled', false)
        ->default('dhtml-translate.ps.enabled', false)
        ->default('dhtml-translate.pl.enabled', false)
        ->default('dhtml-translate.pt.enabled', false)
        ->default('dhtml-translate.ro.enabled', false),

    (new Extend\Settings())
        ->default('dhtml-translate.ru.enabled', false)
        ->default('dhtml-translate.sd.enabled', false)
        ->default('dhtml-translate.si.enabled', false)
        ->default('dhtml-translate.sk.enabled', false)
        ->default('dhtml-translate.sl.enabled', false)
        ->default('dhtml-translate.sm.enabled', false)
        ->default('dhtml-translate.so.enabled', false)
        ->default('dhtml-translate.sq.enabled', false)
        ->default('dhtml-translate.sr.enabled', false)
        ->default('dhtml-translate.st.enabled', false)
        ->default('dhtml-translate.su.enabled', false)
        ->default('dhtml-translate.sv.enabled', false)
        ->default('dhtml-translate.sw.enabled', false)
        ->default('dhtml-translate.ta.enabled', false)
        ->default('dhtml-translate.te.enabled', false)
        ->default('dhtml-translate.th.enabled', false)
        ->default('dhtml-translate.tl.enabled', false)
        ->default('dhtml-translate.tr.enabled', false)
        ->default('dhtml-translate.uk.enabled', false)
        ->default('dhtml-translate.ur.enabled', false)
        ->default('dhtml-translate.uz.enabled', false)
        ->default('dhtml-translate.vi.enabled', false)
        ->default('dhtml-translate.xh.enabled', false)
        ->default('dhtml-translate.yo.enabled', false)
        ->default('dhtml-translate.zh.enabled', false)
        ->default('dhtml-translate.zt.enabled', false)
        ->default('dhtml-translate.zu.enabled', false),


    (new Extend\Validator(PostValidator::class))
        ->configure(function ($flarumValidator, $validator) {
            $rules = $validator->getRules();

            if (!array_key_exists('content', $rules)) {
                return;
            }

            // Retrieve the post limit from settings
            $settings = resolve(SettingsRepositoryInterface::class);
            $postLimit = $settings->get('dhtml-translate.post-limit', 65535); // Default to 10000 if not set

            $rules['content'] = array_map(function(string $rule) use ($postLimit) {
                if (preg_match('/max:\d+/', $rule)) {
                    return 'max:' . $postLimit;
                }
                return $rule;
            }, $rules['content']);

            $validator->setRules($rules);

            $validator->setCustomMessages([
                'content.max' =>  resolve(\Flarum\Locale\Translator::class)->trans('dhtml-translate.forum.post.content.max'),
            ]);
        }),

];
