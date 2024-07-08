import app from 'flarum/admin/app';

app.initializers.add('dhtml/translate', () => {
  //console.log('[dhtml/translate] Hello, admin!');

  const engineOptions = {
    libre: 'Libre Translator',
    microsoft: 'Microsoft Translator',
    google: 'Google Translator',
  };

  app.extensionData.for('dhtml-translate')
    .registerSetting({
      setting: 'dhtml-translate.plugin.engine',
      label: app.translator.trans('dhtml-translate.admin.settings.engine.label'),
      help: app.translator.trans('dhtml-translate.admin.settings.engine.help'),
      type: 'select',
      options: engineOptions,
      default: 'libre',
    })
    .registerSetting({
      setting: 'dhtml-translate.cronKey',
      label: app.translator.trans('dhtml-translate.admin.settings.cronKey'),
      type: 'text',
      required: true,
      help: app.translator.trans('dhtml-translate.admin.settings.plugin.cronHelp'),
      default: '12345',
    })
    .registerSetting({
      setting: 'dhtml-translate.googleApiKey',
      label: app.translator.trans('dhtml-translate.admin.settings.googleApiKey'),
      type: 'text',
      required: true,
    })
    .registerSetting({
      setting: 'dhtml-translate.microsoftApiKey',
      label: app.translator.trans('dhtml-translate.admin.settings.microsoftApiKey'),
      type: 'text',
      required: true,
    })
    .registerSetting({
      setting: 'dhtml-translate.libreApiKey',
      label: app.translator.trans('dhtml-translate.admin.settings.libreApiKey'),
      type: 'text',
      required: true,
    })
    .registerSetting({
      setting: 'dhtml-translate.libreRestTime',
      label: app.translator.trans('dhtml-translate.admin.settings.libreRestTime'),
      type: 'text',
      required: true,
    })
    .registerSetting({
      setting: 'dhtml-translate.rate-limit',
      label: app.translator.trans('dhtml-translate.admin.settings.rate-limit.label'),
      type: 'number',
      required: true,
      help: app.translator.trans('dhtml-translate.admin.settings.rate-limit.help'),
      default: 60,
    })
    .registerSetting({
      setting: 'dhtml-translate.post-limit',
      label: app.translator.trans('dhtml-translate.admin.settings.post-limit.label'),
      type: 'number',
      required: true,
      help: app.translator.trans('dhtml-translate.admin.settings.post-limit.help'),
      default: 65535,
    })

    .registerSetting({
      setting: 'dhtml-translate.subdomains',
      label: app.translator.trans('dhtml-translate.admin.settings.subdomains'),
      type: 'boolean',
      required: true,
      help: app.translator.trans('dhtml-translate.admin.settings.plugin.subdomains'),
    })

    .registerSetting({
      setting: 'dhtml-translate.cronLess',
      label: app.translator.trans('dhtml-translate.admin.settings.cronLess'),
      type: 'boolean',
      required: true,
    })

    .registerSetting({
      setting: 'dhtml-translate.locales',
      label: app.translator.trans('dhtml-translate.admin.settings.language-menu'),
      type: 'text',
      required: true,
    })

    .registerSetting({
      setting: 'dhtml-translate.af.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.af'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.am.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.am'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ar.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ar'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.as.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.as'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.az.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.az'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ba.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ba'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.be.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.be'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.bg.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.bg'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.bn.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.bn'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.bs.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.bs'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ca.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ca'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.cs.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.cs'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.cy.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.cy'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.da.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.da'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.de.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.de'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.dv.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.dv'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.el.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.el'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.en.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.en'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.eo.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.eo'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.es.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.es'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.et.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.et'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.eu.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.eu'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.fa.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.fa'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.fi.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.fi'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.fil.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.fil'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.fj.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.fj'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.fo.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.fo'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.fr.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.fr'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ga.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ga'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.gl.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.gl'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.gu.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.gu'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ha.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ha'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.he.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.he'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.hi.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.hi'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.hr.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.hr'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.hu.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.hu'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.hy.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.hy'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.id.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.id'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ig.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ig'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.is.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.is'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.it.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.it'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ja.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ja'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ka.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ka'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.kk.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.kk'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.km.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.km'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.kn.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.kn'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ko.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ko'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ku.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ku'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ky.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ky'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.lo.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.lo'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.lt.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.lt'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.lv.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.lv'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.mg.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.mg'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.mi.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.mi'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.mk.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.mk'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ml.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ml'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.mn.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.mn'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.mr.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.mr'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ms.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ms'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.mt.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.mt'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.my.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.my'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.nb.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.nb'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ne.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ne'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.nl.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.nl'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.no.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.no'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ny.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ny'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.or.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.or'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.pa.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.pa'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ps.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ps'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.pl.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.pl'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.pt.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.pt'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ro.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ro'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ru.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ru'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.sd.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.sd'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.si.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.si'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.sk.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.sk'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.sl.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.sl'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.sm.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.sm'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.so.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.so'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.sq.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.sq'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.sr.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.sr'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.st.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.st'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.su.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.su'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.sv.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.sv'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.sw.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.sw'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ta.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ta'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.te.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.te'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.th.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.th'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.tl.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.tl'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.tr.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.tr'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.uk.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.uk'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.ur.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.ur'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.uz.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.uz'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.vi.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.vi'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.xh.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.xh'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.yo.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.yo'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.zh.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.zh'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.zt.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.zt'),
      type: 'boolean'
    })
    .registerSetting({
      setting: 'dhtml-translate.zu.enabled',
      label: app.translator.trans('dhtml-translate.admin.settings.plugin.language.zu'),
      type: 'boolean'
    });


});
