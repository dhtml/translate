import app from 'flarum/forum/app';
import addLanguageMenu from "./includes/addLanguageMenu";
import {getSubdomain} from "../common/subdomains";

app.initializers.add('dhtml/translate', () => {

  const subdomain = getSubdomain();
  if(subdomain) {
    const locale = subdomain;
    /*
    if (app.session.user) {
      app.session.user.savePreferences({ locale });
    } else {
      document.cookie = `locale=${locale}; path=/; expires=Tue, 19 Jan 2038 03:14:07 GMT`;
    }
     */
  }

  addLanguageMenu();
});
