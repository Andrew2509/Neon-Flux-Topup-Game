<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['title', 'subtitle', 'description', 'tags', 'wa_link', 'ig_link', 'fb_link', 'image_path', 'link', 'status', 'clicks'];
}
