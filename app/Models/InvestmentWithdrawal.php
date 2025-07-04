<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentWithdrawal extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['method_details'];

    protected $casts = [
      'customer_id' => 'integer',
      'withdrawal_amount' => 'float',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function getMethodDetailsAttribute()
    {
        return json_decode($this->withdrawal_method_details);
    }

    public function getPaidAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
