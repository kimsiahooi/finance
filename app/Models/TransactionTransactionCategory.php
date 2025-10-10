<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionTransactionCategory extends Pivot
{
    use SoftDeletes, HasFactory;

    protected $table = 'transaction_category';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
