<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Exception\ServiceException;
use App\MyResponse;
use App\Request\LoginRequest;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Qbhy\HyperfAuth\AuthManager;

#[Controller('index')]
class IndexController extends AbstractController
{
    #[Inject]
    protected UserService $userService;
    #[Inject]
    protected AuthManager $authManager;

    #[PostMapping('login')]
    public function login(LoginRequest $loginRequest): array
    {
        $data = $loginRequest->validated();
        $user = $this->userService->userLogin($data);
        if ($data['type'] === 'merchant') {
            $merchant = $this->userService->getMerchantByUserId($user->id);
            if ($merchant == null) {
                throw new ServiceException('商户不存在');
            }
            $token = $this->authManager->guard('merchant_jwt')->login($user);
        } else {
            $token = $this->authManager->login($user);
        }
        return MyResponse::success([
            'token' => $token
        ])->toArray();
    }
}
