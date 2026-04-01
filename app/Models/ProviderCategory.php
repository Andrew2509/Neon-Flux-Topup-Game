<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderCategory extends Model
{
    protected $fillable = ['provider', 'ext_id', 'name', 'status'];
}
