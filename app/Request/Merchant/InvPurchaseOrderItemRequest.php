<?php

declare(strict_types=1);

namespace App\Request\Merchant;

use Hyperf\Validation\Request\FormRequest;

class InvPurchaseOrderItemRequest extends FormRequest
{
    use BaseMerchant;
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
            'id' => 'string',
            'sku_id' => 'required',
            'quantity' => 'required',
        ];
    }


    public function messages(): array
    {
        return [
            'id.string' => 'ID不正确',
            'sku_id.required' => '缺少sku_id',
            'quantity.required' => '缺少数量',
        ];
    }
}
