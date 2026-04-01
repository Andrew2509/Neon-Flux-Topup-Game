<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['name', 'provider_id', 'api_key', 'balance', 'status', 'icon', 'mode'];
}
