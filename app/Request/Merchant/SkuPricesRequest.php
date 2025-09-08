<?php

namespace App\Request\Merchant;

use Hyperf\Validation\Request\FormRequest;

class SkuPricesRequest extends FormRequest
{

    use BaseMerchant;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku_id' => 'required|string',
            'channel_id' => 'required|string',
            'price' => 'required|numeric',
        ];
    }


    public function messages(): array
    {
        return [
            'sku_id.required' => 'SKU ID不能为空',
            'sku_id.string' => 'SKU ID不正确',
            'channel_id.required' => '渠道ID不能为空',
            'channel_id.string' => '渠道ID不正确',
            'price.required' => '价格不能为空',
            'price.numeric' => '价格不正确',
        ];
    }


}