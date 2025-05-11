<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'tax_id',
        'address',
        'phone',
        'email',
        'notes',
        'password'
    ];

    protected $casts = [
        'tax_id' => 'encrypted',
        'email_verified_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function ledgerEntries(): MorphMany
    {
        return $this->morphMany(LedgerEntry::class, 'transactionable');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Invoice::class,
            'customer_id', // Foreign key on invoices table
            'invoice_id', // Foreign key on payments table
            'id', // Local key on customers table
            'id' // Local key on invoices table
        );
    }
}