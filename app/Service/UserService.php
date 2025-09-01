<?php

namespace App\Service;


use App\Exception\ServiceException;
use App\Model\InvItemSpu;
use App\Model\MdMerchant;
use App\Model\MdUser;

class UserService
{


    public function register(array $data): MdUser
    {
        if($this->getUserByUsername($data['username']) != null){
            throw new ServiceException('用户已存在');
        }
        $user = new MdUser();
        $user->username = $data['username'];
        $user->password = md5($data['password']);
        $user->save();
        return $user;
    }

    public function getUserByUsername(string $username): ?MdUser
    {
        return MdUser::where('username', $username)->first();
    }

    public function userLogin($data): MdUser
    {
        $user = MdUser::where('username', $data['username'])->first();
        if ($user == null) {
            throw new ServiceException('用户名/密码错误');
        }
        if ($user->password !== md5($data['password'])) {
            throw new ServiceException('用户名/密码错误');
        }
        return $user;
    }

    public function getMerchantByUserId($user_id)
    {
        return MdMerchant::where('user_id', $user_id)->first();
    }


}