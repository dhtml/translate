import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import LocaleDropdown from 'flarum/forum/components/LocaleDropdown';
import Button from 'flarum/common/components/Button';
import HeaderSecondary from 'flarum/common/components/HeaderSecondary';
import SelectDropdown from 'flarum/common/components/SelectDropdown';
import {getSelectLanguages} from "../../common/languages";

export const modifyURL = (url, newSubdomain) => {
  try {
    // Parse the URL
    let urlObj = new URL(url);

    // Split the hostname into parts
    let hostParts = urlObj.hostname.split('.');

    // If the hostname has more than 2 parts, remove the subdomain
    if (hostParts.length > 2) {
      hostParts.shift(); // Remove the first part (subdomain)
    }

    // Create the new hostname with the custom subdomain
    let newHostname = newSubdomain + '.' + hostParts.join('.');

    // Update the URL object with the new hostname
    urlObj.hostname = newHostname;

    // Return the modified URL
    return urlObj.toString();
  } catch (error) {
    console.error('Invalid URL:', error);
    return null;
  }
};

export default function () {


  extend(HeaderSecondary.prototype, 'items', function (items) {
    // Clear existing items

    const languages = getSelectLanguages();

    /*
    const languages = {
      "en": "English",
      "ar": "العربية",
      "zh": "中文",
      "fr": "Français",
      "de": "Deutsch",
      "hi": "हिन्दी",
      "pt": "Português",
      "ru": "Русский",
      "es": "Español",
    };
    */


    const locales = [];

    for (const locale in languages) {
      locales.push(
        <Button
          active={app.data.locale === locale}
          icon={app.data.locale === locale ? 'fas fa-check' : true}
          onclick={() => {
            const newUrl = modifyURL(location.href,locale);
            location.href=newUrl;
          }}
        >
          {languages[locale]}
        </Button>
      );
    }


    items.add(
      'locale',
      <SelectDropdown
        buttonClassName="Button Button--link"
        accessibleToggleLabel={app.translator.trans('core.forum.header.locale_dropdown_accessible_label')}
      >
        {locales}
      </SelectDropdown>,
      20
    );

  });
}
