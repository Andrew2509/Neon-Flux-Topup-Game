<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'product_name',
        'stars',
        'comment',
        'author_nickname',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function displayName(): string
    {
        $nick = trim((string) $this->author_nickname);

        return $nick !== '' ? $nick : (string) ($this->user?->name ?: 'Pelanggan');
    }
}
