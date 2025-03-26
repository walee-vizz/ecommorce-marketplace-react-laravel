<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap = false;

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
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'image' => $this->getFirstMediaUrl('images', 'small'),
            'images' => $this->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'thumb' => $media->getUrl('thumb'),
                    'small' => $media->getUrl('small'),
                    'large' => $media->getUrl('large'),
                ];
            }),
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
            'variationTypes' => $this->variationTypes->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'type' => $type->type,
                    'options' => $type->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'name' => $option->name,
                            'images' => $option->getMedia('images')->map(function ($media) {
                                return [
                                    'id' => $media->id,
                                    'thumb' => $media->getUrl('thumb'),
                                    'small' => $media->getUrl('small'),
                                    'large' => $media->getUrl('large'),
                                ];
                            })
                        ];
                    }),
                ];
            }),
            'variations' => $this->variations->map(function ($variation) {
                return [
                    'id' => $variation->id,
                    'variation_type_option_ids' => $variation->variation_type_option_ids,
                    'price' => $variation->price,
                    'quantity' => $variation->quantity,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        return parent::toArray($request);
    }
}
