<?php

namespace App\Http\Controllers\payments\mpesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;


class MpesaController extends Controller
{
    public function getAccessToken(){
        
        #V1
        // curl_setopt_array(
        //     $curl,
        //     array(
        //         CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf8'],
        //         CURLOPT_RETURNTRANSFER =>true,
        //         CURLOPT_HEADER =>false,
        //         CURLOPT_USERPWD => env('MPESA_CONSUMER_KEY'). ':' . env('MPESA_CONSUMER_SECRET')
        //     )
        // );

        #V2 
        $url = env('MPESA_ENV') == 0
        ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
        : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic '.
            base64_encode(
                env('MPESA_CONSUMER_KEY'). ':' . env('MPESA_CONSUMER_SECRET')
            )
        ));
        curl_setopt($curl, CURLOPT_HEADER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);
        $access_token = json_decode($curl_response);
        return $access_token;

        #V3
        // $curl = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        // curl_setopt($curl, CURLOPT_HTTPHEADER, [
        //     'Authorization: Basic ' . 
        //     base64_encode(
        //         env('MPESA_CONSUMER_KEY'). ':' . env('MPESA_CONSUMER_SECRET')
        //     )
        // ]);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // $response = curl_exec($curl);
        // return json_decode($response)->access_token;
    }

    /**
     * Lipa na M-PESA password
     * */

    public function mpesaPassword(){
        $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        $passkey = env('MPESA_PASSKEY');
        $BSC = '174379';
        $timestamp = $lipa_time;

        $lipa_na_mpesa_password = base64_encode($BSC.$passkey.$timestamp);
        return $lipa_na_mpesa_password;
    }

    /**
     * Lipa na M-PESA STK Push method
     * */

    public function stkPush(){
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json', 
            'Authorization:Bearer'.
            $this->getAccessToken()
            )
        );

        $curl_post_data = [
            'BusinessShortCode' => env('MPESA_STK_SHORTCODE'), 
            'Password' => $this->mpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => 1,
            'PartyA' => env('MPESA_TEST_MSISDN'),
            'PartyB' => env('MPESA_STK_SHORTCODE'),
            'PhoneNumber' => env('MPESA_TEST_MSISDN'),
            'CallBackURL' => env('NGROK_DOMAIN'). '/v1/daraja/stk',
            'AccountReference' => 'STK123',
            'TransactionDesc' => 'Testing stk push',
        ];

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $response = curl_exec($curl);

        return $response;
    }
}
