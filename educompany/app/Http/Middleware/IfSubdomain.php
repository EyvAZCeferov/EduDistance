<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class IfSubdomain
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
        if (Session::has("subdomain") || $request->route('subdomain') != null) {
            $url = $request->url();
            if (Session::has('subdomain') == false)
                Session::put('subdomain', $request->route('subdomain'));


            $urlParts = parse_url($url);
            $host = $urlParts['host'];
            if(env("APP_ENV")=="dev")
                $host=env('APP_DOMAIN');
            $urlWithoutProtocol = preg_replace('~^(?:f|ht)tps?://~i', '', $host);
            if (Session::has('subdomain'))
                return redirect(env('HTTP_OR_HTTPS') . Session::get('subdomain') . '.' . $urlWithoutProtocol);
            else
                return redirect(env('HTTP_OR_HTTPS') .  $urlWithoutProtocol);
        }else{
            return $next($request);
        }
    }
}
