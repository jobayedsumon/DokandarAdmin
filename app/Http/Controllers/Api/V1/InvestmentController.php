<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPackage;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function packages()
    {
        $packages = InvestmentPackage::paginate();
        return response()->json($packages);
    }

    public function package_view($id)
    {
        $package = InvestmentPackage::find($id);
        return response()->json($package);
    }

    public function investments(Request $request)
    {
        $investments = $request->user()->customer_investments()->with('package')->get();
        return response()->json($investments);
    }

    public function withdrawals(Request $request)
    {
        $withdrawals = $request->user()->investment_withdrawals()->get();
        return response()->json($withdrawals);
    }
}
