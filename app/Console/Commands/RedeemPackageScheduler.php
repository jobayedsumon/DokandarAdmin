<?php

namespace App\Console\Commands;

use App\Models\CustomerInvestment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RedeemPackageScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redeem:package';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redeem investment package after their duration in months';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try
        {
            $customer_investments = CustomerInvestment::where('redeemed_at', null)->get();

            foreach ($customer_investments as $investment) {
                $created_at         = Carbon::parse($investment->created_at);
                $duration_in_months = $investment->package->duration_in_months;
                $redeemable_at      = $created_at->addMonths($duration_in_months);

                if ($redeemable_at->isPast()) {
                    $investment->redeemed_at = now();
                    $investment->save();
                    Log::info("Investment Package #$investment->id redeemed successfully.");
                } else {
                    Log::info("Investment Package #$investment->id not redeemed.");
                }
            }

            $this->line('Investment Package Redeem scheduler executed successfully.');
        }
        catch (\Exception $exception)
        {
            Log::error($exception->getMessage());
        }
    }
}
