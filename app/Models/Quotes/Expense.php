<?php

namespace App\Models\Quotes;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    public $timestamps = false;
    protected $table = 'quote_expenses';
    protected $fillable = [
        'cost',
        'concept',
        'quantity',
        'quote_id'
    ];
}
