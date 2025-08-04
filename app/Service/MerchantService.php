<?php

namespace App\Service;


use App\Exception\ServiceException;
use App\Model\Merchant;

class MerchantService
{

    public function login(string $username, string $password)
    {
        $merchant = Merchant::query()->where('username', $username)->first();
        if ($merchant == null) {
            throw new ServiceException('用户名/密码错误');
        }
        if ($merchant->password != sha1($password)) {
            throw new ServiceException('用户名/密码错误');
        }
        return $merchant;
    }


}