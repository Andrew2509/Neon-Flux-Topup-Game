<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name', 'code', 'type', 'image', 'fee', 'account_number', 'status', 'provider'];
}
