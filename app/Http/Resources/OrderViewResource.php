<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderViewResource extends JsonResource
{
    // public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'userId' => $this->user_id,
            'status' => $this->status,
            'totalPrice' => $this->total_price,
            'vendorUser' => new VendorUserResource($this->vendorUser),
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            'orderItems' => $this->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'product' => [
                        'id' => $item->product->id,
                        'title' => $item->product->title,
                        'slug' => $item->product->slug,
                        'description' => $item->product->description,
                        'image' => $item->product->getImageForOptions($this->variation_type_option_ids ?: []),
                    ],
                ];
            })->toArray(),
        ];

        return $data;
    }
}
