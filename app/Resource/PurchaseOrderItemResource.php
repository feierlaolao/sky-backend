<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class PurchaseOrderItemResource extends JsonResource
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
            'total_price' => $this->total_price,
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'base_quantity' => $this->base_quantity,
            'base_unit_price' => $this->base_unit_price,
            'sku' => SkuResource::make($this->whenLoaded('sku')),
        ];
    }
}
