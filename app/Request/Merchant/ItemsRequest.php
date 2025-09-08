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
            //spu信息
            'name' => 'required|string',
            'category_id' => 'required|string',
            'brand_id' => 'string',
            'description' => 'string',
            'images' => 'array',
            'skus' => 'array',
            //父sku
            'skus.*.id' => 'string',
            'skus.*.name' => 'required|string',
            'skus.*.barcode' => 'string',
            'skus.*.conversion_to_base' => 'required|numeric',
//            'skus.*.prices' => 'array',
//            'skus.*.prices.*.channel_id' => 'required|string',
//            'skus.*.prices.*.price' => 'required|numeric',
            //子sku
            'skus.*.children.*.id' => 'string',
            'skus.*.children' => 'array',
            'skus.*.children.*.name' => 'required|string',
            'skus.*.children.*.barcode' => 'string',
            'skus.*.children.*.conversion_to_base' => 'required|numeric',
//            'skus.*.children.*.prices' => 'array',
//            'skus.*.children.*.prices.*.channel_id' => 'required|string',
//            'skus.*.children.*.prices.*.price' => 'required|numeric',
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