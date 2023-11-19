<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // table
    protected $table = 'payments';

    // fillable fields
    protected $fillable = [
        'transaction_id',
        'amount',
        'paid_on',
        'status',
        'details',
    ];

    //transaction
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
