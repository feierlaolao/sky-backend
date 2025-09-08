<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class ItemSkuPriceResource extends JsonResource
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
            'type' => $this->type,
            'channel_id' => $this->channel_id,
            'price' => $this->price
        ];
    }
}
