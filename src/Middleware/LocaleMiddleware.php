<?php

namespace Dhtml\Translate\Middleware;

use Dhtml\Translate\Services\LocaleService;
use Flarum\Http\RequestUtil;
use Flarum\Locale\LocaleManager;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class LocaleMiddleware implements Middleware
{
    /**
     * @var LocaleManager
     */
    protected $locales;

    /**
     * @param LocaleManager $locales
     */
    public function __construct(LocaleManager $locales)
    {
        $this->locales = $locales;
        $this->localeService = new LocaleService();
        $this->defaultLocale = $this->localeService->getCurrentLocale();
        $this->settings = resolve(SettingsRepositoryInterface::class);
    }

    public function process(Request $request, Handler $handler): Response
    {
        if(!isset($_SERVER['HTTP_HOST'])) {return $handler->handle($request);}

        $host = $_SERVER['HTTP_HOST'];

        $parts = explode('.', $host);

        // Check if there are enough parts to have a subdomain
        if (count($parts) >= 3) {
            // The subdomain is the first part
            $subdomain = $parts[0];

            $is_enabled = $this->settings->get("dhtml-translate.{$subdomain}.enabled");
            if(!$is_enabled) {
                //detected locale is not enabled
                $pageUrl = $this->getFullUrl();
                $rootDomain = str_replace( "://{$subdomain}.", "://", $pageUrl);
                $this->redirectTo($rootDomain);
            }

        } else {
            // No subdomain present
            $subdomain = '';
        }

        $locale = $subdomain;
        $defaultLocale = $this->localeService->getCurrentLocale();
        $preferredLocale = $this->getUserPreferredLanguage();

        if($subdomain == $defaultLocale && $subdomain==$preferredLocale) {
            //you cant be english and still be viewing en.africoders.com
            $pageUrl = $this->getFullUrl();
            $rootDomain = str_replace( "://{$subdomain}.", "://", $pageUrl);
            $this->redirectTo($rootDomain);
        }

        if(empty($locale) || strlen($locale)!="2") {
            $locale = $defaultLocale; //get default e.g en
        }

        if($subdomain=="") {
            if ($preferredLocale != $defaultLocale) {
                $pageUrl = $this->getFullUrl();
                $newSubDomain = str_replace( "://", "://$preferredLocale.", $pageUrl);
                $this->redirectTo($newSubDomain);
            }
        } else {
            //set locale to subdomain if not empty
            $locale = $subdomain;
        }


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



        return $handler->handle($request);
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
