<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_user_id',
        'stripe_session_id',
        'total_price',
        'status',
        'online_payment_commission',
        'website_commission',
        'vendor_subtotal',
        'payment_intent'
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function vendorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_user_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_user_id', 'user_id');
    }
}
