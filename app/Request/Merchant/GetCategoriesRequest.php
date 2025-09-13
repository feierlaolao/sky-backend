<?php

declare(strict_types=1);

namespace App\Request\Merchant;


use App\Request\PaginatedFormRequest;

class GetCategoriesRequest extends PaginatedFormRequest
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
            'name' => 'string|max:64',
            'sort_by' => 'in:created_at',
        ]);
    }


    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.string' => '分类名称不正确',
            'name.max' => '分类名称不能大于64个字符',
            'sortBy.in' => '排序字段不正确',
        ]);
    }



}
