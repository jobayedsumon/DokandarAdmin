<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentPayment extends Model
{
    use HasFactory;

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function package()
    {
        return $this->belongsTo(InvestmentPackage::class, 'investment_id');
    }
}
