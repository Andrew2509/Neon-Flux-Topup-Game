<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['name', 'description', 'price', 'discount', 'status'];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
}
