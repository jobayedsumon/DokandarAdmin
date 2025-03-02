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

class EasyPaymentController extends Controller
{
    use Processor;

    private PaymentRequest $payment;
    private mixed $config_values;
    private User   $user;
    private PendingRequest $client;

    private string $storeId;
    private string $userName;
    private string $password;
    private string $hashKey;


    public function __construct(PaymentRequest $payment, User $user)
    {
        $this->payment = $payment;
        $this->user = $user;

        $baseUrl = '';

        $config = $this->payment_config('eps', 'payment_config');

        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
            $baseUrl             = 'https://pgapi.eps.com.bd/v1/';
            $this->storeId       = $this->config_values->storeId;
            $this->userName      = $this->config_values->userName;
            $this->password      = $this->config_values->password;
            $this->hashKey       = $this->config_values->hashKey;
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
            $baseUrl             = 'https://sandboxpgapi.eps.com.bd/v1/';
            $this->storeId       = 'd44e705f-9e3a-41de-98b1-1674631637da';
            $this->userName      = 'Epsdemo@gmail.com';
            $this->password      = 'Epsdemo258@';
            $this->hashKey       = 'FHZxyzeps56789gfhg678ygu876o=';
        }

        $this->client = Http::baseUrl($baseUrl);
    }

    private function generateHash(string $data): string
    {
        return base64_encode(hash_hmac('sha512', utf8_encode($data), utf8_encode($this->hashKey), true));
    }

    private function getToken(): string
    {
        $response = $this->client
            ->withHeader('x-hash', $this->generateHash($this->userName))
            ->post('Auth/GetToken', [
                'userName' => $this->userName,
                'password' => $this->password
            ]);

        return $response['token'];
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
            'storeId'               => $this->storeId,
            'merchantTransactionId' => (string)time(),
            'CustomerOrderId'       => $data['id'],
            'transactionTypeId'     => $data['payment_platform'] === 'web' ? 1 : 2,
            'totalAmount'           => round($data['payment_amount'], 2),
            'successUrl'            => route('eps.success', ['payment_id' => $data['id']]),
            'failUrl'               => route('eps.fail', ['payment_id' => $data['id']]),
            'cancelUrl'             => route('eps.cancel', ['payment_id' => $data['id']]),
            'customerName'          => $payer_information->name ?: 'Dokandar',
            'customerEmail'         => $payer_information->email ?: 'admin@dokandar.online',
            'customerAddress'       => $payer_information->address ?: 'Savar',
            'customerCity'          => 'Savar',
            'customerState'         => 'Savar',
            'customerPostcode'      => '1340',
            'customerCountry'       => 'Bangladesh',
            'customerPhone'         => $payer_information->phone ?: '01688007454',
            'productName'           => 'Dokandar',
        ];

        $response = $this->client
            ->withToken($this->getToken())
            ->replaceHeaders(['x-hash' => $this->generateHash($postData['merchantTransactionId'])])
            ->post('EPSEngine/InitializeEPS', $postData);

        dd($response['RedirectURL']);

//        return redirect()->away($response['RedirectURL']);
    }

    public function success(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        if ($request->input('Status') === 'Success') {

            $this->payment::where(['id' => $request['payment_id']])->update([
                'payment_method' => 'eps',
                'is_paid'        => 1,
                'transaction_id' => $request->input('EPSTransactionId')
            ]);

            $data = $this->payment::where(['id' => $request['payment_id']])->first();

            if (isset($data) && function_exists($data->success_hook)) {
                call_user_func($data->success_hook, $data);
            }
            return $this->payment_response($data, 'success');
        }
        $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data, 'fail');
    }

    public function fail(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data, 'fail');
    }

    public function cancel(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data, 'cancel');
    }
}
