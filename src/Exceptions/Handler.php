<?php

namespace RotHub\Laravel\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as LaravelHandler;
use Illuminate\Validation\ValidationException;
use RotHub\Laravel\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Response as Http;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends LaravelHandler
{
    /**
     * @inheritdoc
     */
    public function render($request, \Throwable $e)
    {
        if ($e instanceof Exception) {
            $error = $e;
        } else if ($e instanceof NotFoundHttpException) {
            $error = $this->notFoundHttpException($e);
        } else if ($e instanceof ValidationException) {
            $error = $this->validationException($e);
        } else if ($e instanceof ModelNotFoundException) {
            $error = $this->modelNotFoundException($e);
        } else if ($e instanceof \PDOException) {
            $error = $this->pdoException($e);
        } else {
            $error = $this->defaultException($e);
        }

        $res['code'] = $error->getCode();
        $res['message'] = $error->getMessage();
        config('app.debug') and $res['trace'] = $e->getTrace();

        return response($res, $error->getStatusCode());
    }

    /**
     * 地址异常.
     * 
     * @param NotFoundHttpException $e 异常.
     * @return Exception
     */
    protected function notFoundHttpException(NotFoundHttpException $e): Exception
    {
        $status = $e->getStatusCode();
        $message = '地址错误.';

        return $this->fail($message, $status, $status);
    }

    /**
     * 验证异常.
     * 
     * @param ValidationException $e 异常.
     * @return Exception
     */
    protected function validationException(ValidationException $e): Exception
    {
        $message = $e->validator->errors()->first();

        return $this->fail($message, $e->status, $e->status);
    }

    /**
     * 模型异常.
     * 
     * @param ModelNotFoundException $e 异常.
     * @return Exception
     */
    protected function modelNotFoundException(ModelNotFoundException $e): Exception
    {
        $status = Http::HTTP_PRECONDITION_REQUIRED;

        $class = $e->getModel();
        if (class_exists($class)) {
            $name = (new \ReflectionClass($class))->getShortName();
            $message = $name . ' 数据未查到.';
        } else {
            $message = '数据未查到.';
        }

        config('app.debug') and $message = $e->getMessage();

        return $this->fail($message, $status, $status);
    }

    /**
     * SQL 异常.
     * 
     * @param \PDOException $e 异常.
     * @return Exception
     */
    protected function pdoException(\PDOException $e): Exception
    {
        $code = $e->getCode();
        $status = Http::HTTP_INTERNAL_SERVER_ERROR;
        $message = Http::$statusTexts[$status];

        config('app.debug') and $message = $e->getMessage();

        return $this->fail($message, $code, $status);
    }

    /**
     * 默认异常.
     * 
     * @param \Throwable $e 异常.
     * @return Exception
     */
    protected function defaultException(\Throwable $e): Exception
    {
        $status = Http::HTTP_INTERNAL_SERVER_ERROR;
        $message = Http::$statusTexts[$status];

        config('app.debug') and $message = $e->getMessage();

        return $this->fail($message, $status, $status);
    }

    /**
     * 失败异常.
     * 
     * @param string $message 错误信息.
     * @param mixed $code 错误编码.
     * @param mixed $status 状态编码.
     * @return Exception
     */
    protected function fail($message, $code, $status): Exception
    {
        return Exception::fake($message, (int)$code, (int)$status);
    }
}
