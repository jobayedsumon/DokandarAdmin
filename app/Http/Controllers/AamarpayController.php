<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Models\User;
use App\Traits\Processor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AamarpayController extends Controller
{
    use Processor;

    private PaymentRequest $payment;
    private $config_values;
    private $user;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('aamarpay', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }

        $this->payment = $payment;
        $this->user = $user;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        $payment_amount = $data['payment_amount'];
        $payer_information = json_decode($data['payer_information']);

        $url = $this->config_values->mode == 'live' ? 'https://secure.aamarpay.com/request.php' : 'https://sandbox.aamarpay.com/request.php';
        $fields = array(
            'store_id' => $this->config_values->mode == 'live' ? $this->config_values->store_id : 'aamarpay',
            'amount' => round($payment_amount, 2), //transaction amount
            'payment_type' => 'VISA', //no need to change
            'currency' => $data['currency_code'],  //currenct will be USD/BDT
            'tran_id' => Str::random(6) . '-' . rand(1, 1000), //transaction id must be unique from your end
            'cus_name' => $payer_information->name,  //customer name
            'cus_email' => $payer_information->email && $payer_information->email != '' ? $payer_information->email : 'example@example.com', //customer email address
            'cus_add1' => 'Savar',  //customer address
            'cus_add2' => 'Savar', //customer address
            'cus_city' => 'Savar',  //customer city
            'cus_state' => 'Savar',  //state
            'cus_postcode' => '1340', //postcode or zipcode
            'cus_country' => 'Bangladesh',  //country
            'cus_phone' => $payer_information->phone == null ? '0000000000' : $payer_information->phone, //customer phone number
            'cus_fax' => 'Not¬Applicable',  //fax
            'ship_name' => $payer_information->name, //ship name
            'ship_add1' => 'Savar',  //ship address
            'ship_add2' => 'Savar',
            'ship_city' => 'Savar',
            'ship_state' => 'Savar',
            'ship_postcode' => '1340',
            'ship_country' => 'Bangladesh',
            'desc' => 'payment description',
            'success_url' => route('aamarpay.success', ['payment_id' => $data['id']]), //your success route
            'fail_url' => route('aamarpay.fail', ['payment_id' => $data['id']]), //your fail route
            'cancel_url' => route('aamarpay.cancel', ['payment_id' => $data['id']]), //your cancel url
            'opt_a' => 'A',  //optional paramter
            'opt_b' => 'B',
            'opt_c' => 'C',
            'opt_d' => 'D',
            'signature_key' => $this->config_values->mode == 'live' ? $this->config_values->signature_key : '28c78bb1f45112f5d40b956fe104645a',
        );

        $fields_string = http_build_query($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $url_forward = str_replace('"', '', stripslashes(curl_exec($ch)));
        curl_close($ch);

        $this->redirect_to_merchant($url_forward);
    }

    function redirect_to_merchant($url) {

        ?>
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head><script type="text/javascript">
                function closethisasap() { document.forms["redirectpost"].submit(); }
            </script></head>
        <body onLoad="closethisasap();">

        <form name="redirectpost" method="post" action="<?php echo ($this->config_values->mode == 'live' ? 'https://secure.aamarpay.com/' : 'https://sandbox.aamarpay.com/') . $url; ?>"></form>
        <!-- for live url https://secure.aamarpay.com -->
        </body>
        </html>
        <?php
        exit;
    }

    public function success(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        if ($request->input('pay_status') == 'Successful') {

            $this->payment::where(['id' => $request['payment_id']])->update([
                'payment_method' => 'aamarpay',
                'is_paid' => 1,
                'transaction_id' => $request->input('mer_txnid')
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
