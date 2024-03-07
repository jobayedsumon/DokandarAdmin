<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\CentralLogics\SMS_module;
use App\Http\Controllers\Controller;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Library\Receiver;
use App\Models\BusinessSetting;
use App\Models\CustomerInvestment;
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

    public function my_investments(Request $request)
    {
        $investments = $request->user()->customer_investments()->with('package')->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        $withdrawals = $request->user()->investment_withdrawals()->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        $investment_wallet = $request->user()->investment_wallet;

        return response()->json([
            'investments' => [
                'total_size' => $investments->total(),
                'limit' => $request['limit'],
                'offset' => $request['offset'],
                'investments' => $investments->items()
            ],
            'withdrawals' => [
                'total_size' => $withdrawals->total(),
                'limit' => $request['limit'],
                'offset' => $request['offset'],
                'withdrawals' => $withdrawals->items()
            ],
            'investment_wallet' => $investment_wallet
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

        if (!isset($customer)) {
            return response()->json(['errors' => ['message' => 'Customer not found']], 403);
        }

        $investmentPackage = InvestmentPackage::find($request->package_id);
        $investmentPaymentAmount = $investmentPackage->amount;

        if (!isset($investmentPaymentAmount)) {
            return response()->json(['errors' => ['message' => 'Amount not found']], 403);
        }

        $investmentPayment = new InvestmentPayment();
        $investmentPayment->customer_id = $customer->id;
        $investmentPayment->investment_id = $investmentPackage->id;
        $investmentPayment->amount = $investmentPaymentAmount;
        $investmentPayment->payment_status = 'pending';
        $investmentPayment->payment_method = $request->payment_method;
        $investmentPayment->save();

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

    public function my_investment_view($id)
    {
        $myInvestment = request()->user()->customer_investments()->find($id);
        return response()->json($myInvestment);
    }

    public function redeem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'investment_id' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $investment = $request->user()->customer_investments()->find($request->investment_id);
        if (!$investment) {
            return response()->json(['errors' => ['message' => 'Investment not found']], 403);
        }
        if ($investment->redeemed_at) {
            return response()->json(['errors' => ['message' => 'Already redeemed']], 403);
        }

        $investment->redeemed_at = now();
        $investment->save();

        try
        {
            $msg = 'Your investment of '.$investment->package->amount.' ৳ of investment package '.$investment->package->name.' has been redeemed successfully.';
            SMS_module::send_custom_sms($request->user()->phone, $msg);
        } catch (\Exception $exception) {
            info($exception->getMessage());
        }

        return response()->json($investment);
    }

    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'withdrawal_amount' => 'required|numeric|min:1',
            'method_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $customer = User::find($request->user()->id);
        $investmentWallet = $customer->investment_wallet;
        if ($investmentWallet->balance < $request->withdrawal_amount) {
            return response()->json(['errors' => ['message' => 'Insufficient balance']], 403);
        }

        $withdrawal = $customer->investment_withdrawals()->create([
            'withdrawal_amount'         => $request->withdrawal_amount,
            'withdrawal_method_details' => json_encode($request->except(['withdrawal_amount'])),
        ]);

        $msg = 'Your withdrawal request of '.$withdrawal->withdrawal_amount.' ৳ has been received successfully. You will be notified once it is processed.';
        SMS_module::send_custom_sms($request->user()->phone, $msg);

        return response()->json($withdrawal);
    }

    public function transfer_to_wallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $customer = User::find($request->user()->id);
        $investmentWallet = $customer->investment_wallet;
        if ($investmentWallet->balance < $request->amount) {
            return response()->json(['errors' => ['message' => 'Insufficient balance']], 403);
        }

        $transfer = CustomerLogic::create_wallet_transaction($customer->id, $request->amount, 'investment_to_wallet', '');
        if ( ! $transfer) {
            return response()->json(['errors' => ['message' => 'Transfer failed']], 403);
        }

        $msg = 'Your investment balance of '.$request->amount.' ৳ has been transferred to your D-Wallet successfully.';
        SMS_module::send_custom_sms($request->user()->phone, $msg);

        return response()->json(['message' => 'Transfer successful']);
    }
}
