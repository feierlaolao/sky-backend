<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class FileUploadRequest extends FormRequest
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
            'type' => 'required|in:image',
            'extension' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($this->input('type') === 'image') {
                        in_array($value, ['jpg', 'jpeg', 'png', 'gif']) ?: $fail('扩展不正确');;
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => '文件类型不存在',
            'type.in' => '文件类型不正确',
            'extension.required' => '扩展不存在',
        ];
    }
}
