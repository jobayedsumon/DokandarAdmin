<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Models\User;
use App\Traits\Processor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class PayStationController extends Controller
{
    use Processor;

    private PaymentRequest $payment;
    private mixed $config_values;
    private User   $user;
    private PendingRequest $client;

    private string $storeId;
    private string $password;


    public function __construct(PaymentRequest $payment, User $user)
    {
        $this->payment = $payment;
        $this->user = $user;

        $baseUrl = '';

        $config = $this->payment_config('paystation', 'payment_config');

        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
            $baseUrl             = 'https://api.paystation.com.bd';
            $this->storeId       = $this->config_values->storeId;
            $this->password      = $this->config_values->password;
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
            $baseUrl             = 'https://sandbox.paystation.com.bd';
            $this->storeId       = '104-1653730183';
            $this->password      = 'gamecoderstorepass';
        }

        $this->client = Http::baseUrl($baseUrl);
    }

    public function index(Request $request): JsonResponse|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails())
        {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if ( ! isset($data))
        {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        $payer_information = json_decode($data['payer_information']);

        $postData = [
            'invoice_number' => (string)time(),
            'currency' => 'BDT',
            'payment_amount' => round($data['payment_amount'], 2),
            'reference' => $data['id'],
            'cust_name' => $payer_information->name ?: 'Dokandar',
            'cust_phone' => $payer_information->phone ?: '01688007454',
            'cust_email' => $payer_information->email ?: 'admin@dokandar.online',
            'cust_address' => $payer_information->address ?: 'Savar',
            'callback_url' => route('paystation.callback', ['payment_id' => $data['id']]),
            'checkout_items' => '',
            'merchantId' => $this->storeId,
            'password' => $this->password,
        ];

        $response = $this->client->post('/initiate-payment', $postData);

        return redirect()->away($response->object()->payment_url);
    }

    public function callback(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        $status        = $request->input('status');
        $transactionId = $request->input('trx_id');
        $paymentId     = $request->input('payment_id');

        $paymentData = $this->payment::where(['id' => $paymentId])->first();

        if ($status === 'Successful') {

            $this->payment::where(['id' => $paymentId])->update([
                'payment_method' => 'paystation',
                'is_paid'        => 1,
                'transaction_id' => $transactionId,
            ]);

            if (isset($paymentData) && function_exists($paymentData->success_hook)) {
                call_user_func($paymentData->success_hook, $paymentData);
            }

            return $this->payment_response($paymentData, 'success');
        }

        if (isset($paymentData) && function_exists($paymentData->failure_hook)) {
            call_user_func($paymentData->failure_hook, $paymentData);
        }

        return $this->payment_response($paymentData, $status === 'Canceled' ? 'cancel' : 'fail');
    }
}
