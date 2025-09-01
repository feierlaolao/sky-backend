<?php

namespace App\Exception\Handler;

use App\Exception\ServiceException;
use App\MyResponse;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

class ServiceExceptionHandler extends ExceptionHandler
{

    public function handle(Throwable $throwable, ResponsePlusInterface $response): ResponsePlusInterface
    {
        $this->stopPropagation();
        $response = $response->setHeader('content-type', 'application/json; charset=utf-8');
        return $response
            ->withStatus(500)
            ->withBody(new SwooleStream(
                MyResponse::error($throwable->getMessage(), 500)->json()
            ));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ServiceException;
    }
}