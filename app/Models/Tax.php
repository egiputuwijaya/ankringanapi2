<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    /** @use HasFactory<\Database\Factories\TaxFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'type',
        'outlet_id',
        'apply_to',
        'active',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'active' => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
