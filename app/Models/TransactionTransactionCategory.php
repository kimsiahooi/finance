<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionTransactionCategory extends Pivot
{
    use SoftDeletes, HasFactory;

    protected $table = 'transaction_category';

    protected $fillable = ['user_id', 'transaction_id', 'category_id'];
}
