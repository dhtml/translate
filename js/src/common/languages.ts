import app from 'flarum/common/app';

export const locales: string[] = [
  "af", "am", "ar", "as", "az", "ba", "be", "bg", "bn", "bs", "ca", "cs", "cy", "da", "de", "dv", "el", "en", "eo", "es", "et", "eu", "fa", "fi", "fil", "fj", "fo", "fr", "ga", "gl", "gu", "ha", "he", "hi", "hr", "hu", "hy", "id", "ig", "is", "it", "ja", "ka", "kk", "km", "kn", "ko", "ku", "ky", "lo", "lt", "lv", "mg", "mi", "mk", "ml", "mn", "mr", "ms", "mt", "my", "nb", "ne", "nl", "no", "ny", "or", "pa", "ps", "pl", "pt", "ro", "ru", "sd", "si", "sk", "sl", "sm", "so", "sq", "sr", "st", "su", "sv", "sw", "ta", "te", "th", "tl", "tr", "uk", "ur", "uz", "vi", "xh", "yo", "zh", "zt", "zu"
];

export const selectedLocale = (): string[] => {
  const languages = app.forum.attribute('dhtmlLanguageMenu');
  return languages?.split(',').map((locale: string) => locale.trim());
};



// Mapping of locale codes to language names
const localeToLanguage: { [key: string]: string } = {
  "af": "Afrikaans",
  "am": "አማርኛ",
  "ar": "العربية",
  "as": "অসমীয়া",
  "az": "Azərbaycan",
  "ba": "Башҡорт",
  "be": "Беларуская",
  "bg": "Български",
  "bn": "বাংলা",
  "bs": "Bosanski",
  "ca": "Català",
  "cs": "Čeština",
  "cy": "Cymraeg",
  "da": "Dansk",
  "de": "Deutsch",
  "dv": "ދިވެހި",
  "el": "Ελληνικά",
  "en": "English",
  "eo": "Esperanto",
  "es": "Español",
  "et": "Eesti",
  "eu": "Euskara",
  "fa": "فارسی",
  "fi": "Suomi",
  "fil": "Filipino",
  "fj": "Na Vosa Vakaviti",
  "fo": "Føroyskt",
  "fr": "Français",
  "ga": "Gaeilge",
  "gl": "Galego",
  "gu": "ગુજરાતી",
  "ha": "Hausa",
  "he": "עברית",
  "hi": "हिन्दी",
  "hr": "Hrvatski",
  "hu": "Magyar",
  "hy": "Հայերեն",
  "id": "Indonesia",
  "ig": "Igbo",
  "is": "Íslenska",
  "it": "Italiano",
  "ja": "日本語",
  "ka": "ქართული",
  "kk": "Қазақ",
  "km": "ភាសាខ្មែរ",
  "kn": "ಕನ್ನಡ",
  "ko": "한국어",
  "ku": "Kurdî",
  "ky": "Кыргызча",
  "lo": "ລາວ",
  "lt": "Lietuvių",
  "lv": "Latviešu",
  "mg": "Malagasy",
  "mi": "Māori",
  "mk": "Македонски",
  "ml": "മലയാളം",
  "mn": "Монгол",
  "mr": "मराठी",
  "ms": "Melayu",
  "mt": "Malti",
  "my": "မြန်မာစာ",
  "nb": "Norsk Bokmål",
  "ne": "नेपाली",
  "nl": "Nederlands",
  "no": "Norsk",
  "ny": "ChiCheŵa",
  "or": "ଓଡ଼ିଆ",
  "pa": "ਪੰਜਾਬੀ",
  "ps": "پښتو",
  "pl": "Polski",
  "pt": "Português",
  "ro": "Română",
  "ru": "Русский",
  "sd": "سنڌي",
  "si": "සිංහල",
  "sk": "Slovenčina",
  "sl": "Slovenščina",
  "sm": "Gagana Samoa",
  "so": "Soomaali",
  "sq": "Shqip",
  "sr": "Српски",
  "st": "Sesotho",
  "su": "Basa Sunda",
  "sv": "Svenska",
  "sw": "Kiswahili",
  "ta": "தமிழ்",
  "te": "తెలుగు",
  "th": "ไทย",
  "tl": "Tagalog",
  "tr": "Türkçe",
  "uk": "Українська",
  "ur": "اردو",
  "uz": "Oʻzbek",
  "vi": "Tiếng Việt",
  "xh": "isiXhosa",
  "yo": "Yorùbá",
  "zh": "中文",
  "zt": "中文(繁體)",
  "zu": "isiZulu"
};

export const getSelectLanguages = () => {

  const languages: { [key: string]: string } = {};

  for (const locale of selectedLocale()) {
      languages[locale] = localeToLanguage[locale];
  }

  return languages;
};
