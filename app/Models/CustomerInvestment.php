<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInvestment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['customer', 'package'];

    protected $appends = ['profit_earned'];

    protected $casts = [
        'customer_id' => 'integer',
        'investment_id' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function package()
    {
        return $this->belongsTo(InvestmentPackage::class, 'investment_id');
    }

    public function getProfitEarnedAttribute()
    {
        $until = $this->redeemed_at ? Carbon::parse($this->redeemed_at) : now();
        $days  = $until->diffInDays($this->created_at);

        return $this->package->daily_profit * $days;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getRedeemedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }
}
