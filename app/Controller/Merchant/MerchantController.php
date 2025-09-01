<?php

namespace App\Controller\Merchant;

use App\Controller\AbstractController;
use App\MyResponse;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Qbhy\HyperfAuth\AuthManager;
use Qbhy\HyperfAuth\AuthMiddleware;

#[Controller(prefix: "m/merchant")]
#[Middleware(AuthMiddleware::class)]
class MerchantController extends AbstractController
{
    #[Inject]
    protected AuthManager $authManager;
    #[Inject]
    protected UserService $userService;
    #[GetMapping('info')]
    public function info(): array
    {
        $user = $this->authManager->user();
        $merchant = $this->userService->getMerchantByUserId($user->id);
        return MyResponse::success([
            'id' => $merchant->id,
            'user_id' => $user->id,
        ])->toArray();
    }
}