<?php
declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

abstract class PaginatedFormRequest extends FormRequest
{
    // 统一输入（别名→标准键）
    protected function validationData(): array
    {
        $all = parent::validationData();

        // alias 归一化
        $all['current']  = $all['current']  ?? $all['page']      ?? 1;
        $all['page_size'] = $all['page_size'] ?? $all['page_size'] ?? 20;

        return $all;
    }

    public function rules(): array
    {
        return [
            'current'  => ['sometimes','integer','min:1'],
            'page_size' => ['sometimes','integer','min:1','max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'current'  => '页码',
            'page_size' => '每页数量',
        ];
    }

    public function messages(): array
    {
        return [
            'current.integer'  => ':attribute 必须是整数',
            'current.min'      => ':attribute 不能小于 :min',
            'page_size.integer' => ':attribute 必须是整数',
            'page_size.min'     => ':attribute 不能小于 :min',
            'page_size.max'     => ':attribute 不能大于 :max',
        ];
    }

    /** 便捷拿到干净值（含默认） */
    public function pageParams(): array
    {
        $v = $this->validated();
        return [
            'current'  => (int)($v['current']  ?? 1),
            'page_size' => (int)($v['page_size'] ?? 20),
        ];
    }
}
