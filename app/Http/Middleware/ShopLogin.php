<?php

namespace App\Http\Middleware;

use Closure;

class ShopLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(session()->has('shop_id'))
        {
            session()->keep(['shop_id','shop_name']);
            return $next($request);
        }else{
            abort(404);
        }
    }
}
