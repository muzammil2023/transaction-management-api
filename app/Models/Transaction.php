<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    //table
    protected $table = 'transactions';

    //fillable
    protected $fillable = [
        'amount',
        'status',
        'due_on',
        'is_vat_inclusive',
        'user_id',
        'vat',
        'description',
        'transaction_id',
    ];

    //payment relation
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    //user relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function amountPaid()
    {
        return $this->payments()->where('transaction_id', $this->id)->sum('amount');
    }

    public function grossAmount()
    {
        if (!$this->is_vat_inclusive) {
            $grossAmount = $this->amount + ($this->amount * ($this->vat / 100));
        } else {
            $grossAmount = $this->amount;
        }

        return $grossAmount;
    }
}
