<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
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
            'order_date' => $this->order_date,
            'total_amount' => $this->total_amount,
            'quantity' => $this->quantity,
            'items' => PurchaseOrderItemResource::collection($this->whenLoaded('items')),
            'channel' => ChannelResource::make($this->whenLoaded('channel')),
        ];
    }
}
