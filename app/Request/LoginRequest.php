<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the User is authorized to make this request.
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
            'username' => 'required',
            'password' => 'required',
            'type' => 'required|in:user,merchant',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '用户名不能为空',
            'password.required' => '密码不能为空',
            'type.required' => '登陆类型不能为空',
            'type.in' => '登陆类型不存在',
        ];
    }
}
