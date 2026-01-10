<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $default = 'en';

        if (Schema::hasTable('languages')) {
            $language = Language::query()
                ->whereIsDefault(Language::IS_DEFAULT)
                ->first();

            $default = $language?->code ?? 'en';
        }

        $locale = session('locale', $default);

        App::setLocale($locale);

        return $next($request);
    }
}
