<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPackage;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function packages(Request $request)
    {
        $packages = InvestmentPackage::where([
            'type'   => $request['type'],
            'status' => 1
        ])->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);

        return response()->json([
            'total_size' => $packages->total(),
            'limit'      => $request['limit'],
            'offset'     => $request['offset'],
            'packages'   => $packages->items()
        ]);
    }

    public function package_view($id)
    {
        $package = InvestmentPackage::find($id);
        return response()->json($package);
    }

    public function investments(Request $request)
    {
        $investments = $request->user()->customer_investments()->with('package')->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        return response()->json([
            'total_size' => $investments->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'investments' => $investments->items()
        ]);
    }

    public function withdrawals(Request $request)
    {
        $withdrawals = $request->user()->investment_withdrawals()->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        return response()->json([
            'total_size' => $withdrawals->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'withdrawals' => $withdrawals->items()
        ]);
    }
}
