<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $supported = config('locales.supported', ['en']);
        $fallback = config('app.fallback_locale', 'en');

        $sessionLocale = $request->session()->get('locale');
        if (is_string($sessionLocale) && in_array($sessionLocale, $supported, true)) {
            $locale = $sessionLocale;
        } else {
            $locale = config('app.locale', $fallback);
        }

        if (! in_array($locale, $supported, true)) {
            $locale = in_array($fallback, $supported, true) ? $fallback : $supported[0];
        }

        App::setLocale($locale);

        if (class_exists(\Carbon\Carbon::class)) {
            \Carbon\Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
