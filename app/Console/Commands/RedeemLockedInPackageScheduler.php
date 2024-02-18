<?php

namespace App\Console\Commands;

use App\Models\CustomerInvestment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RedeemLockedInPackageScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redeem:locked_in';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redeem locked in package after a certain time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try
        {
            $locked_in_investments = CustomerInvestment::where('redeemed_at', null)
                ->whereHas('package', function ($q) {
                    $q->where('type', 'locked-in');
                })->get();

            foreach ($locked_in_investments as $investment) {
                $created_at         = $investment->created_at;
                $duration_in_months = $investment->package->duration_in_months;
                $redeemable         = $created_at->addMonths($duration_in_months);
                if ($redeemable->isPast())
                {
                    $investment->redeemed_at = now();
                    $investment->save();

                    Log::info('Locked in package redeemed successfully.');
                }

                Log::info('Locked in package not redeemed.');
            }

            Log::info('Locked in package scheduler executed successfully.');
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
        }
    }
}
