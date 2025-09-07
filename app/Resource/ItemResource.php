<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class ItemResource extends JsonResource
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
            'description' => $this->description,
            'attr' => $this->attr,
            'category' => $this->when($this->relationLoaded('category') && $this->category, fn() => CategoryResource::make($this->category)),
            'brand' => $this->when($this->relationLoaded('brand') && $this->brand, fn() => BrandResource::make($this->brand)),
            'skus'=> SkuResource::collection($this->whenLoaded('skus')),
            'images' => ImageResource::collection($this->whenLoaded('images'))
        ];
    }
}
