<?php

namespace App\Http\Controllers\payments\mpesa;

use App\Http\Controllers\Controller;
use App\Models\MpesaTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        curl_close($curl);
        return $access_token->access_token;

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
        $BSC = env('MPESA_STK_SHORTCODE');
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
            'Authorization: Bearer'.' '.
            $this->getAccessToken(),
            'Content-Type:application/json'
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
            'AccountReference' => 'Alvo',
            'TransactionDesc' => 'Testing stk push',
        ];

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function createValidationResponse($result_code, $result_description){
        $result = json_encode([
            'ResultCode' => $result_code,
            'ResultDesc' => $result_description
        ]);

        $response = new Response();
        $response->headers->set('Content-Type',  'application/json; charset=utf-8');
        $response->setContent($result);
        return $response;
    }

    public function mpesaValidation(){
        $result_code = '0';
        $result_description = 'Accepted';
        $this->createValidationResponse($result_code, $result_description);
    }

    public function mpesaConfirmation(Request $request){
        $content = json_decode($request->getContent());

        $mpesa = new MpesaTransaction();
        $mpesa->TransactionType = $content->TransactionType;
        $mpesa->TransactionID = $content->TransID;
        $mpesa->TransTime = $content->TransTime;
        $mpesa->BusinessShortCode = $content->BusinessShortCode;
        $mpesa->BillRefNumber = $content->BillRefNumber;
        $mpesa->InvoiceNumber = $content->InvoiceNumber;
        $mpesa->OrgAccountBalance = $content->OrgAccountBalance;
        $mpesa->ThirdPartyTransID = $content->ThirdPartyTransID;
        $mpesa->MSISDN = $content->MSISDN;
        $mpesa->FirstName = $content->FirstName;
        $mpesa->MiddleName = $content->MiddleName;
        $mpesa->LastName = $content->LastName;
        $mpesa->save();

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=utf-8');
        $response->setContent(json_encode([
            'C2BPaymentConfirmationResult' => 'Success'
        ]));

        return $response;
    }
}
