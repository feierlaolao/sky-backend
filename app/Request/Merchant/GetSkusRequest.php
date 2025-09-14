<?php

declare(strict_types=1);

namespace App\Request\Merchant;


use App\Request\PaginatedFormRequest;

class GetSkusRequest extends PaginatedFormRequest
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
        return array_merge(parent::rules(), [
            'name' => 'string',
            'channel_id' => 'string',
            'barcode' => 'string',
            'sort_by' => 'in:created_at',
        ]);
    }


    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'channel_id.string' => '渠道ID不正确',
            'barcode.string' => '条形码不正确',
            'name.string' => '产品名称不正确',
            'sort_by.in' => '排序字段不正确',
        ]);
    }


}
