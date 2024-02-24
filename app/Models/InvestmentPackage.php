<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentPackage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['monthly_profit', 'daily_profit'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getMonthlyProfitAttribute()
    {
        return $this->amount * $this->monthly_interest_rate / 100;
    }

    public function getDailyProfitAttribute()
    {
        return $this->monthly_profit / 30;
    }

    public function getImageAttribute($value)
    {
        return asset('storage/app/public/investment').'/'.$value;
    }
}
