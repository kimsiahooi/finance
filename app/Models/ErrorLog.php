<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = [
        'level',
        'type',
        'location',
        'input',
        'message',
        'trace',
        'context',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'input' => 'array',
        ];
    }
}
