<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    //



    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function childrens()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }


    public function scopeIsActive($query, $active = true)
    {
        return $query->where('active', $active);
    }
}
