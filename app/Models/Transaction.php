<?php

namespace App\Models;

use App\Enums\Transaction\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = ['name', 'remark', 'type', 'amount', 'transaction_at'];
    protected $appends = ['type_display'];

    protected function casts(): array
    {
        return [
            'transaction_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(TransactionCategory::class)->withTimestamps();
    }

    public function getTypeDisplayAttribute()
    {
        return Type::from($this->type)?->display();
    }
}
