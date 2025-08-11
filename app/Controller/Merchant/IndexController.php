<?php

namespace App\Controller\Merchant;

use App\Controller\AbstractController;
use App\Model\InvItemSku;
use App\Model\InvSpu;
use App\MyResponse;
use App\Request\LoginRequest;
use App\Service\MerchantService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Qbhy\HyperfAuth\AuthManager;

#[Controller(prefix: "merchant/index")]
class IndexController extends AbstractController
{

    #[Inject]
    protected MerchantService $merchantService;

    #[Inject]
    protected AuthManager $authManager;

    #[RequestMapping('test')]
    public function Test()
    {
        $sku = new InvItemSku();
        $sku->spu_id = '812835307215925249';
        $sku->name = '1箱';
        $sku->barcode = '123456789';
        $sku->conversion_to_base = 15;
        $sku->price = 10;
        $sku->save();
    }

    #[PostMapping('login')]
    public function login(LoginRequest $loginRequest)
    {
        $data = $loginRequest->validated();
        $merchant = $this->merchantService->login($data['username'], $data['password']);
        $token = $this->authManager->login($merchant);
        return MyResponse::getInstance(data: [
            'id' => $merchant->id,
            'username' => $merchant->username,
            'token' => $token
        ])->build();

    }

}