<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{


    public function scopeIsActive($query, $active = true)
    {
        return $query->where('active', $active);
    }
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
