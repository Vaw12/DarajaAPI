<?php

namespace App\Http\Controllers\payments\mpesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class MpesaController extends Controller
{
    public function getAccessToken(){
        $url = env('MPESA_ENV') == 0
        ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
        : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init($url);
        // curl_setopt_array(
        //     $curl,
        //     array(
        //         CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf8'],
        //         CURLOPT_RETURNTRANSFER =>true,
        //         CURLOPT_HEADER =>false,
        //         CURLOPT_USERPWD => env('MPESA_CONSUMER_KEY'). ':' . env('MPESA_CONSUMER_SECRET')
        //     )
        // );

        curl_setopt(
            $curl, 
            CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . base64_encode(env('MPESA_CONSUMER_KEY'). ':' . env('MPESA_CONSUMER_SECRET'))
            ]
        );
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        return json_decode($response);

        // $response = curl_exec($curl);
        // curl_close($curl);

        // return $response;
    }
}
