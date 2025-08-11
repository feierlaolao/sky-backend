<?php

declare(strict_types=1);

namespace App\Request;


class ItemsRequest extends PaginatedFormRequest
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
        return array_merge(parent::rules(), [
            'name' => '',
            'sortBy' => '',
        ]);
    }


    public function messages(): array
    {
        return array_merge(parent::messages(), [

        ]);
    }
}
