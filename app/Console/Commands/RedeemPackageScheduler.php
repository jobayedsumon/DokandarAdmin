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
    protected $description = 'Redeem package after a certain time';

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
                $redeemable         = $created_at->addMonths($duration_in_months);
                if ($redeemable->isPast())
                {
                    $investment->redeemed_at = now();
                    $investment->save();

                    Log::info('Package redeemed successfully.');
                }

                Log::info('Package not redeemed.');
            }

            Log::info('Package scheduler executed successfully.');
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
        }
    }
}
