<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    // public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'price' => $this->price,
            'stock' => $this->stock,
            'status' => $this->status,
            'image' => $this->getFirstMediaUrl('images', 'small'),
            'department' => [
                'id' => $this->department->id,
                'name' => $this->department->name,
            ],
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ],
            'vendor' => [
                'user_id' => $this->user?->vendor?->user_id ?? $this->user?->id,
                'store_name' => $this->user?->vendor?->store_name ?? $this->user?->name,
                'store_address' => $this->user?->vendor?->store_address ?? null,
                'status' => $this->user?->vendor?->status,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
