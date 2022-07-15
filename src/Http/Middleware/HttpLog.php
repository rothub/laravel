<?php

namespace RotHub\Laravel\Http\Middleware;

use Illuminate\Support\Facades\Log;

class HttpLog
{
    /**
     * @inheritdoc
     */
    public function handle($request, \Closure $next)
    {
        Log::debug('Request', [
            'url' => $request->getPathInfo(),
            'request' => $request->all(),
        ]);

        $response = $next($request);

        Log::debug('Response', [
            'url' => $request->getPathInfo(),
            'response' => $response->getContent(),
        ]);

        return $response;
    }
}
