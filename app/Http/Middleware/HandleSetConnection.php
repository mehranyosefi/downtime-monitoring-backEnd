<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class HandleSetConnection
{
    const FA_LOCALE = "fa";
    const FA_DATABASE = "mysqlFa";

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = App::getLocale();
        if ($locale === self::FA_LOCALE) {
            DB::setDefaultConnection(self::FA_DATABASE);
        }
        return $next($request);
    }
}
