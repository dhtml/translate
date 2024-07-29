<?php

namespace Dhtml\Translate\Services;

use Flarum\Http\RequestUtil;
use Flarum\Locale\LocaleManager;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class RedirectService
{
    /**
     * @var LocaleManager
     */
    protected $locales;

    public function __construct(LocaleManager $locales)
    {
        $this->locales = $locales;
        $this->localeService = new LocaleService();
        $this->defaultLocale = $this->localeService->getCurrentLocale();
        $this->settings = resolve(SettingsRepositoryInterface::class);
    }

    public function process(Request $request, Handler $handler): Response
    {
        //no http at all
        if(!isset($_SERVER['HTTP_HOST'])) {return $handler->handle($request);}

        //detect subdomain
        $subdomain = getDetectedSubdomain();
        $currentLocale = $this->localeService->getCurrentLocale();
        $defaultLocale = getDefaultLocale();

        //no subdomain, dont bother
        if(empty($subdomain)) {
            //we are on the root
            $preferredLocale = $this->getUserPreferredLanguage();

            if($currentLocale != $preferredLocale) {
                //redirect to ar.africoders.com e.t.c
                $url = $this->getPreferredUrl($preferredLocale);
                $this->redirectTo($url);
            }

        } else {
            //subdomain is detected so change the language to that locale
            if($subdomain == $defaultLocale) {
                //if default flarum locale is en and the current subdomain is en.
                $url = $this->getBaseUrl($defaultLocale);
                $this->redirectTo($url);
            } else {
                //change the locale to what we are currently viewing
                $this->changeDefaultLocale($request,$currentLocale);
            }
        }


        return $handler->handle($request);
    }

    function changeDefaultLocale(Request &$request, $locale) {
        $actor = RequestUtil::getActor($request);

        if ($actor->exists) {
            //for currently logged in users, must update their locale settings first
            $_locale = $actor->getPreference('locale');
            if($_locale!=$locale) {
                $actor->setPreference('locale', $locale);
                $actor->save();
            }
        }


        $this->locales->setLocale($locale);

        setCurrentTranslationLocale($locale);
        $request = $request->withAttribute('locale', $this->locales->getLocale());

        return $request;
    }

    function getPreferredUrl($subdomain="") {
        $pageUrl = $this->getFullUrl();
        return str_replace( "://","://{$subdomain}.",  $pageUrl);
    }

    function getBaseUrl($subdomain="") {
        $pageUrl = $this->getFullUrl();
        return str_replace( "://{$subdomain}.","://",  $pageUrl);
    }

    function getUserPreferredLanguage()
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return $this->defaultLocale; // Default to English if header not present
        }

        $languages = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

        // Parse languages from the header (first language code)
        $preferredLanguage = explode(',', $languages)[0];
        $preferredLanguage = strtolower(substr($preferredLanguage, 0, 2)); // Extract first two characters (language code)

        $is_enabled = $this->settings->get("dhtml-translate.{$preferredLanguage}.enabled");
        if(!$is_enabled) {
            //detected locale is not enabled
            $preferredLanguage = $this->defaultLocale;
        }

        // Optionally, you can validate $preferredLanguage against a list of supported languages
        return $preferredLanguage;
    }

    private function getFullUrl()
    {
        static $fullUrl = null;
        if($fullUrl) {return $fullUrl;}

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';

        // Host
        $host = $_SERVER['HTTP_HOST'];

        // Request URI (including query string if present)
        $requestUri = $_SERVER['REQUEST_URI'];

        // Full URL
        $fullUrl = $protocol . $host . $requestUri;
        return $fullUrl;
    }

    private function redirectTo($url) {
        header("Location: $url", true, 302);
        exit();
    }
}
