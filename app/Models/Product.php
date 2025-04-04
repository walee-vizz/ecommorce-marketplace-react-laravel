<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;




    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(100);
        $this->addMediaConversion('small')->width(480);
        $this->addMediaConversion('large')->width(1200);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function variationTypes(): HasMany
    {
        return $this->hasMany(VariationType::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }
    public function scopeIsPublished($query)
    {
        return $query->where('status', ProductStatusEnum::Published);
    }
    public function scopeIsDraft($query)
    {
        return $query->where('status', ProductStatusEnum::Draft);
    }
    public function scopeIsOutOfStock($query)
    {
        return $query->where('status', ProductStatusEnum::OutOfStock)->orWhere('stock', '<=', 0);
    }
    public function scopeForVendor($query)
    {
        return $query->where('created_by', Auth::user()->id);
    }

    public function scopeForWebsite($query)
    {
        return $query->isPublished();
    }

    public function getPriceForOptions($options = [])
    {

        $optionIds = array_values($options);
        sort($optionIds);
        if (empty($optionIds)) {
            return $this->price;
        }
        foreach ($this->variations as $variation) {

            $a = $variation->variation_type_option_ids;
            sort($a);
            if ($a == $optionIds && !empty($variation->price)) {
                return $variation->price;
            }
            return $this->price;
        }
    }
}
