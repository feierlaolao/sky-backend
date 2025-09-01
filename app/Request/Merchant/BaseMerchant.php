<?php

namespace App\Request\Merchant;

trait BaseMerchant
{
    public function validatedWithMerchant(): array
    {
        return array_merge($this->validated(), [
            'merchant_id' => auth()->guard('merchant_jwt')->id() ?? null,
        ]);
    }
}