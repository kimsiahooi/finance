<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionCategory extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionCategoryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'transaction_category', 'category_id')
            ->withTimestamps()
            ->withPivot(['id', 'user_id', 'deleted_at'])
            ->wherePivotNull('deleted_at')
            ->using(TransactionTransactionCategory::class);
    }
}
