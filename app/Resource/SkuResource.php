<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class SkuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'barcode' => $this->barcode,
            'base_sku_id' => $this->base_sku_id,
            'conversion_to_base' => $this->conversion_to_base,
            'attrs' => $this->attrs,
            'cost_price' => $this->const_price,
            'stock_quantity' => $this->stock_quantity,
            'spu' => ItemResource::make($this->whenLoaded('spu')),
            'children' => SkuResource::collection($this->whenLoaded('children')),
        ];
    }
}
