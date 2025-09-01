<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\MyResponse;
use App\Request\UserRegisterRequest;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Qbhy\HyperfAuth\AuthManager;

#[Controller('user/index')]
class IndexController extends AbstractController
{

    #[Inject]
    protected UserService $userService;

    #[Inject]
    protected AuthManager $authManager;

    #[PostMapping('register')]
    public function register(UserRegisterRequest $registerRequest): array
    {
        $data = $registerRequest->validated();
        $user = $this->userService->register($data);
        $token = $this->authManager->login($user);
        return MyResponse::success([
            'id' => $user->id,
            'username' => $user->username,
            'token' => $token,
        ])->toArray();

    }

}