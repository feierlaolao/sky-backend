<?php

declare(strict_types=1);

namespace App\Request\Merchant;


use App\Request\PaginatedFormRequest;

class GetSkuPricesRequest extends PaginatedFormRequest
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
            'type' => 'in:0,1',
            'channel_id' => 'string',
            'sortBy' => 'in:created_at',
        ]);
    }


    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'type.in' => '类型不正确',
            'channel_id.string' => '渠道ID不正确',
            'sortBy.in' => '排序字段不正确',
        ]);
    }


}
