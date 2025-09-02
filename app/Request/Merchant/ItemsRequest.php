<?php

namespace App\Request\Merchant;

use Hyperf\Validation\Request\FormRequest;

class ItemsRequest extends FormRequest
{

    use BaseMerchant;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'category_id' => 'required|string',
            'brand_id' => 'string',
            'description' => 'string',
            'images' => 'array',
            'sku' => 'array',
            'sku.*.name' => 'required|string',
            'sku.*.barcode' => 'string',
            'sku.*.base_sku_id' => 'string',
            'sku.*.conversion_to_base' => 'required|numeric',
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => '产品名称不能为空',
            'name.string' => '产品名称不正确',
            'category_id.required' => '产品分类不能为空',
            'category_id.string' => '产品分类不正确',
            'brand_id.string' => '产品品牌不正确',
            'description.string' => '产品描述不正确',
            'images.array' => '产品图片不正确'
        ];
    }


}