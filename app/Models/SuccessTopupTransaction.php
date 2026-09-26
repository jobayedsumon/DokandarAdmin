<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuccessTopupTransaction extends Model
{
    use HasFactory;

    protected $table = 'success_topup_transactions';

    protected $fillable = [
        'user_id',
        'trxid',
        'service_type',
        'number',
        'operator',
        'amount',
        'status',
        'gateway_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
    ];
}
