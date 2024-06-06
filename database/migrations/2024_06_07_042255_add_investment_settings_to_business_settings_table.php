<?php

use App\Models\BusinessSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            BusinessSetting::updateOrCreate([
               'key' => 'investment_referral_bonus',
            ]);
            BusinessSetting::updateOrCreate([
                'key' => 'investment_withdrawal_charge',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            BusinessSetting::where('key', 'investment_referral_bonus')->delete();
            BusinessSetting::where('key', 'investment_withdrawal_charge')->delete();
        });
    }
};
