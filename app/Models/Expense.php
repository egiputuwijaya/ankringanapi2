<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'outlet_id',
        'user_id',
        'category',
        'amount',
        'expense_date',
        'notes',
        'receipt_image',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
