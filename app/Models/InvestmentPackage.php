<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentPackage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['yearly_profit', 'monthly_profit', 'daily_profit', 'is_invested_by_current_user'];

    protected $casts = [
        'amount'               => 'integer',
        'created_at'           => 'datetime:Y-m-d H:i:s',
        'updated_at'           => 'datetime:Y-m-d H:i:s',
        'yearly_interest_rate' => 'float',
        'duration_in_months'   => 'integer'
    ];

    public function getYearlyProfitAttribute()
    {
        return $this->amount * $this->yearly_interest_rate / 100;
    }

    public function getMonthlyProfitAttribute()
    {
        return $this->yearly_profit / 12;
    }

    public function getDailyProfitAttribute()
    {
        return $this->monthly_profit / 30;
    }

    public function getImageAttribute($value)
    {
        return asset('storage/app/public/investment').'/'.$value;
    }

    public function investments()
    {
        return $this->hasMany(CustomerInvestment::class, 'investment_id');
    }

    public function getIsInvestedByCurrentUserAttribute()
    {
        return $this->investments()->where('customer_id', auth('api')->id())->exists();
    }
}
