<?php

declare(strict_types=1);
/**
 * This file is part of qbhy/hyperf-auth.
 *
 * @link     https://github.com/qbhy/hyperf-auth
 * @document https://github.com/qbhy/hyperf-auth/blob/master/README.md
 * @contact  qbhy0715@qq.com
 * @license  https://github.com/qbhy/hyperf-auth/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use App\MyResponse;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

class AuthExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->stopPropagation();
        return $response
            ->withStatus(401)
            ->withBody(new SwooleStream(
                MyResponse::error('验证失败',401)->json()
            ));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof UnauthorizedException;
    }
}
