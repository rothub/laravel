<?php

namespace RotHub\Laravel\Http\Middleware;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as Http;

class FormaterResponse
{
    /**
     * @inheritdoc
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            return $this->formater($response);
        } else if ($response instanceof Response) {
            $content = $response->getOriginalContent();

            if (is_null($content)) {
                return $this->formater($response);
            }
        }

        return $response;
    }

    /**
     * 格式化.
     * 
     * @param Response|JsonResponse $response 响应.
     * @return mixed
     */
    protected function formater(Response|JsonResponse $response): mixed
    {
        $data = $response->getOriginalContent();

        $res['code'] = Http::HTTP_OK;
        $res['message'] = Http::$statusTexts[Http::HTTP_OK];
        is_null($data) or $res['data'] = $data;

        return response()->json($res);
    }
}
