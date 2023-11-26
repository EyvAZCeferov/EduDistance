<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    protected $table='payments';
    protected $fillable=[
        'token',
        'amount',
        'transaction_id',
        'payment_status',
        'data',
        'frompayment'
    ];
    protected $casts=[
        'amount'=>"decimal",
        'payment_status'=>'boolean',
        'data'=>"json",
        'frompayment'=>'json'
    ];
}
