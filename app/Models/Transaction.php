<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = ['name', 'remark', 'amount', 'transaction_at', 'user_id', 'transaction_type_id'];

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

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }
}
