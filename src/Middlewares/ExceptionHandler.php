<?php

namespace Yakuzan\Boiler\Middlewares;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Yakuzan\Boiler\Traits\ResponseTrait;

class ExceptionHandler
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @throws AuthorizationException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (!empty($response->exception) && $request->expectsJson() && null !== ($json_response = $this->exception($response->exception))) {
            return $json_response;
        }

        return $response;
    }
}
