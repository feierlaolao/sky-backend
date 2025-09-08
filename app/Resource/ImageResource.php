<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;
use function Hyperf\Support\env;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->attachment->id,
            'url' => env('S3_ENDPOINT') . $this->attachment->object_key
        ];
    }
}
