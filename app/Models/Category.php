<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'status', 'type',
        'input_label', 'input_placeholder', 'has_zone', 'zone_label', 'zone_placeholder',
        'ext_id', 'category_ext_id', 'is_popular', 'platform',
        'support_phone',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function jenis()
    {
        return $this->hasMany(ProductJenis::class);
    }

    public function providerCategory()
    {
        return $this->belongsTo(ProviderCategory::class, 'category_ext_id', 'ext_id');
    }
}
