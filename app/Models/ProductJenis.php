<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductJenis extends Model
{
    protected $fillable = ['id', 'category_id', 'name', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
