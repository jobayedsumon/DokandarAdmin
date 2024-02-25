<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Library\Receiver;
use App\Models\BusinessSetting;
use App\Models\InvestmentPackage;
use App\Models\InvestmentPayment;
use App\Models\User;
use App\Models\WalletPayment;
use App\Traits\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function invest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|numeric|min:1',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $customer = User::find($request->user()->id);
        $investmentPackage = InvestmentPackage::find($request->package_id);
        $investmentPaymentAmount = $investmentPackage->amount;

        $investmentPayment = new InvestmentPayment();
        $investmentPayment->customer_id = $customer->id;
        $investmentPayment->investment_id = $investmentPackage->id;
        $investmentPayment->amount = $investmentPaymentAmount;
        $investmentPayment->payment_status = 'pending';
        $investmentPayment->payment_method = $request->payment_method;
        $investmentPayment->save();

        if (!isset($customer)) {
            return response()->json(['errors' => ['message' => 'Customer not found']], 403);
        }

        if (!isset($investmentPaymentAmount)) {
            return response()->json(['errors' => ['message' => 'Amount not found']], 403);
        }

        if (!$request->has('payment_method')) {
            return response()->json(['errors' => ['message' => 'Payment not found']], 403);
        }

        $payer = new Payer(
            $customer->f_name . ' ' . $customer->l_name ,
            $customer->email,
            $customer->phone,
            ''
        );

        $currency=BusinessSetting::where(['key'=>'currency'])->first()->value;
        $additional_data = [
            'business_name' => BusinessSetting::where(['key'=>'business_name'])->first()?->value,
            'business_logo' => asset('storage/app/public/business') . '/' .BusinessSetting::where(['key' => 'logo'])->first()?->value
        ];
        $payment_info = new PaymentInfo(
            success_hook: 'investment_success',
            failure_hook: 'investment_failed',
            currency_code: $currency,
            payment_method: $request->payment_method,
            payment_platform: $request->payment_platform,
            payer_id: $customer->id,
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: $investmentPaymentAmount,
            external_redirect_link: $request->has('callback')?$request['callback']:session('callback'),
            attribute: 'investment_payments',
            attribute_id: $investmentPayment->id
        );

        $receiver_info = new Receiver('receiver_name','example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        $data = [
            'redirect_link' => $redirect_link,
        ];
        return response()->json($data, 200);

    }
}
