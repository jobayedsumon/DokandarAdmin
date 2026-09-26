<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\SuccessTopupTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class TopupController extends Controller
{
    private const BASE_URL = 'https://api.successtopup.com';
    private const SANDBOX_URL = 'https://successtopup.com/api/sandbox/test';

    private function getCredentials(): array
    {
        $key = BusinessSetting::where(['key' => 'success_topup_key'])->first()?->value ?? '';
        $secret = BusinessSetting::where(['key' => 'success_topup_secret'])->first()?->value ?? '';
        return ['key' => $key, 'secret' => $secret];
    }

    private function isEnabled(): bool
    {
        return (BusinessSetting::where(['key' => 'success_topup_status'])->first()?->value ?? 0) == 1;
    }

    private function isLive(): bool
    {
        return (BusinessSetting::where(['key' => 'success_topup_go_live'])->first()?->value ?? 0) == 1;
    }

    private function validateCredentials(): void
    {
        if (!$this->isEnabled()) {
            abort(response()->json([
                'errors' => [
                    ['code' => 'topup_disabled', 'message' => 'Success TopUp is disabled']
                ]
            ], 403));
        }

        $credentials = $this->getCredentials();
        if ($this->isLive() && (empty($credentials['key']) || empty($credentials['secret']))) {
            abort(response()->json([
                'errors' => [
                    ['code' => 'topup_not_configured', 'message' => 'Success TopUp live credentials are not configured']
                ]
            ], 403));
        }
    }

    private function callSuccessTopUp(string $action, string $uri, array $body): array
    {
        $credentials = $this->getCredentials();
        $body['successtopup_key'] = $credentials['key'] ?: 'YOUR_API_KEY';
        $body['successtopup_secret'] = $credentials['secret'] ?: 'YOUR_API_SECRET';

        $url = self::BASE_URL . $uri;
        $requestBody = $body;
        if (!$this->isLive()) {
            $url = self::SANDBOX_URL;
            $requestBody = [
                'action' => $action,
                'payload' => $body,
            ];
        }

        $response = Http::acceptJson()->asJson()->post($url, $requestBody);

        return $response->json() ?? ['result' => false, 'message' => 'No response from Success TopUp'];
    }

    private function determineStatus(array $response): string
    {
        if (($response['result'] ?? false) === true) {
            $message = strtolower($response['message'] ?? '');
            if (str_contains($message, 'processing')) {
                return 'processing';
            }
            return 'success';
        }
        return 'failed';
    }

    public function recharge(Request $request)
    {
        $this->validateCredentials();

        $validator = Validator::make($request->all(), [
            'number' => 'required|string',
            'operator' => 'required|string',
            'type' => 'required|string|in:prepaid,postpaid',
            'amount' => 'required|numeric|min:1',
            'package_id' => 'nullable|string',
            'trxid' => 'required|string|unique:success_topup_transactions,trxid',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = $request->user();
        if ($user->wallet_balance < $request->amount) {
            return response()->json(['errors' => [
                ['code' => 'insufficient_balance', 'message' => 'Insufficient wallet balance']
            ]], 403);
        }

        // Debit wallet first
        CustomerLogic::create_wallet_transaction($user->id, $request->amount, 'topup_recharge', $request->trxid);

        $body = [
            'number' => $request->number,
            'type' => $request->type,
            'operator' => $request->operator,
            'amount' => $request->amount,
            'trxid' => $request->trxid,
        ];

        if ($request->filled('package_id')) {
            $body['package_id'] = $request->package_id;
        }

        $response = $this->callSuccessTopUp('recharge', '/api/recharge', $body);
        $status = $this->determineStatus($response);

        SuccessTopupTransaction::create([
            'user_id' => $user->id,
            'trxid' => $request->trxid,
            'service_type' => 'recharge',
            'number' => $request->number,
            'operator' => $request->operator,
            'amount' => $request->amount,
            'status' => $status,
            'gateway_response' => $response,
        ]);

        if ($status === 'failed') {
            CustomerLogic::create_wallet_transaction($user->id, $request->amount, 'topup_refund', $request->trxid);
        }

        return response()->json($response);
    }

    public function drives(Request $request)
    {
        $this->validateCredentials();

        $validator = Validator::make($request->all(), [
            'operator' => 'nullable|string',
            'type' => 'nullable|string|in:drive,regular',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $body = [];
        if ($request->filled('operator')) {
            $body['operator'] = $request->operator;
        }
        if ($request->filled('type')) {
            $body['type'] = $request->type;
        }

        $response = $this->callSuccessTopUp('drives', '/api/drives', $body);
        return response()->json($response);
    }

    public function billPay(Request $request)
    {
        $this->validateCredentials();

        $validator = Validator::make($request->all(), [
            'billOperator' => 'required|string',
            'billNumber' => 'required|string',
            'billAmount' => 'required|numeric|min:1',
            'mobileNumber' => 'required|string',
            'monthName' => 'required|string',
            'note' => 'nullable|string',
            'trxid' => 'required|string|unique:success_topup_transactions,trxid',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = $request->user();
        if ($user->wallet_balance < $request->billAmount) {
            return response()->json(['errors' => [
                ['code' => 'insufficient_balance', 'message' => 'Insufficient wallet balance']
            ]], 403);
        }

        CustomerLogic::create_wallet_transaction($user->id, $request->billAmount, 'topup_bill', $request->trxid);

        $body = [
            'billOperator' => $request->billOperator,
            'billNumber' => $request->billNumber,
            'billAmount' => $request->billAmount,
            'mobileNumber' => $request->mobileNumber,
            'monthName' => $request->monthName,
            'trxid' => $request->trxid,
        ];

        if ($request->filled('note')) {
            $body['note'] = $request->note;
        }

        $response = $this->callSuccessTopUp('bill-pay', '/api/bill-pay', $body);
        $status = $this->determineStatus($response);

        SuccessTopupTransaction::create([
            'user_id' => $user->id,
            'trxid' => $request->trxid,
            'service_type' => 'bill',
            'number' => $request->billNumber,
            'operator' => $request->billOperator,
            'amount' => $request->billAmount,
            'status' => $status,
            'gateway_response' => $response,
        ]);

        if ($status === 'failed') {
            CustomerLogic::create_wallet_transaction($user->id, $request->billAmount, 'topup_refund', $request->trxid);
        }

        return response()->json($response);
    }

    public function status(Request $request)
    {
        $this->validateCredentials();

        $validator = Validator::make($request->all(), [
            'trxid' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $response = $this->callSuccessTopUp('status', '/api/status', [
            'trxid' => $request->trxid,
        ]);

        if (isset($response['status'])) {
            SuccessTopupTransaction::where('trxid', $request->trxid)->update([
                'status' => $response['status'],
            ]);
        }

        return response()->json($response);
    }

    public function balance()
    {
        $this->validateCredentials();
        $response = $this->callSuccessTopUp('balance', '/api/balance', []);
        return response()->json($response);
    }
}
