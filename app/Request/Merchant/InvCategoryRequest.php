<?php

declare(strict_types=1);

namespace App\Request\Merchant;

use Hyperf\Validation\Request\FormRequest;

class InvCategoryRequest extends FormRequest
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
            'name' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '名称不能为空',
        ];
    }
}
