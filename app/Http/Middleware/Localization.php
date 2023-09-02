<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App;
class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        App::setlocale(backpack_user()->language ?? "ar_SA");        
        return $next($request);
    }
}
