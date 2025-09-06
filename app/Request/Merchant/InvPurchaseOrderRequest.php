<?php

declare(strict_types=1);

namespace App\Request\Merchant;

use Hyperf\Validation\Request\FormRequest;

class InvPurchaseOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'channel_id' => 'required',
            'items' => 'required|array',
            'items.*.sku_id' => 'required',
            'items.*.quantity' => 'required',
        ];
    }


    public function messages(): array
    {
        return [
            'channel_id.required' => '渠道编号不能为空',
            'items.required' => '商品列表不能为空',
            'items.array' => '商品列表不正确',
            'items.*.sku_id.required' => ':attribute 缺少sku_id',
            'items.*.quantity.required' => ':attribute 缺少数量',
        ];
    }
}
