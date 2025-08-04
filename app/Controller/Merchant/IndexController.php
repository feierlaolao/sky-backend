<?php

namespace App\Controller\Merchant;

use App\Controller\AbstractController;
use App\Model\Merchant;
use App\MyResponse;
use App\Request\LoginRequest;
use App\Service\MerchantService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Qbhy\HyperfAuth\AuthManager;

#[Controller(prefix: "merchant")]
class IndexController extends AbstractController
{

    #[Inject]
    protected MerchantService $merchantService;

    #[Inject]
    protected AuthManager $authManager;

    #[PostMapping('login')]
    public function login(LoginRequest $loginRequest)
    {
        $data = $loginRequest->validated();
        $merchant = $this->merchantService->login($data['username'], $data['password']);
        $token = $this->authManager->login($merchant);
        return MyResponse::getInstance(data: [
            'token' => $token
        ])->build();

    }

}