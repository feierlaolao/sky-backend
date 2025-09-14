<?php

declare(strict_types=1);

namespace App\Request\Merchant;


use App\Request\PaginatedFormRequest;

class GetItemsRequest extends PaginatedFormRequest
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
            'name' => '',
            'category_id' => '',
            'channel_id' => '',
            'sort_by' => '',
        ]);
    }


    public function messages(): array
    {
        return array_merge(parent::messages(), [

        ]);
    }
}
