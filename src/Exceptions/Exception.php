<?php

namespace RotHub\Laravel\Exceptions;

use Symfony\Component\HttpFoundation\Response as Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Exception extends HttpException
{
    /**
     * 异常信息: 默认错误信息.
     */
    const FAIL = '操作失败.';

    /**
     * 编码: 请求失败.
     */
    const CODE_BAD = Http::HTTP_BAD_REQUEST;

    /**
     * 工厂.
     * 
     * @param string $message 错误信息.
     * @param int $code 错误编码.
     * @param int $status 状态编码.
     * @return static
     */
    public static function fake(
        string $message = self::FAIL,
        int $code = self::CODE_BAD,
        int $status = self::CODE_BAD
    ): static {
        return new static($status, $message, null, [], $code);
    }

    /**
     * 失败.
     * 
     * @param string $message 错误信息.
     * @param int $code 错误编码.
     * @param int $status 状态编码.
     * @return void
     */
    public static function fail(
        string $message = self::FAIL,
        int $code = self::CODE_BAD,
        int $status = self::CODE_BAD
    ): void {
        throw new static($status, $message, null, [], $code);
    }
}
