<?php

namespace App\Http\Middleware;

use App\Traits\ApiReturnFormatTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CheckApiKeyMiddleware
{
    use ApiReturnFormatTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if($request->hasHeader('apiKey')):
            if($request->header('apiKey') == Config::get('wemaxconfig.api_key')):
                return $next($request);
            endif;
        endif;

        return $this->responseWithError('Invalid Api Key', [], 400);
    }
}
