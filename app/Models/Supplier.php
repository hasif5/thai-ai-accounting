<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tax_id',
        'address',
        'phone',
        'email',
        'notes'
    ];

    protected $casts = [
        'tax_id' => 'encrypted'
    ];

    public function ledgerEntries(): MorphMany
    {
        return $this->morphMany(LedgerEntry::class, 'transactionable');
    }
}