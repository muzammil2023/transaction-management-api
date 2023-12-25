<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'checkin',
        'checkout',
        'no_of_members',
        'message',
    ];

    protected $dates = [
        'checkin',
        'checkout',
    ];

    public static function rules()
    {
        return [
            'name' => 'required|string',
            'phone' => 'required|string',
            'checkin' => 'required|date',
            'checkout' => 'required|date',
            'no_of_members' => 'required|integer',
            'message' => 'nullable|string',
        ];
    }
}
