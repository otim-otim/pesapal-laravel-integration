<?php

namespace OtimOtim\PesapalIntegrationPackage\Services;

use Carbon\Carbon;
use Curl\Curl;
use Exception;



class PesapalService
{
    public $baseUrl = config('APP_ENV') == 'production' ? config('PesapalIntegrationPackage.LIVE_URL') : config('PesapalIntegrationPackage.SAND_BOX_URL');

    public $token = '';

    public $expiry = '';

    

    private function isExpired(): bool{
        if(!$this->token || !$this->expiry)
            return true;
        if(Carbon::now()->subMinutes(5) < Carbon::parse($this->expiry))
            return true;

        return false;
    }

   

    private function getAuthToken(){
        $curl = new Curl();
        $curl->setHeader('Accept', 'application/json');
        $curl->setHeader('Content-Type', 'application/json');
        
        $data = array(
            'consumer_key' => config('PesapalIntegrationPackage.CONSUMER_KEY') ,
            'consumer_secret' => config('PesapalIntegrationPackage.CONSUMER_SECRET')
        );

    
        $curl->post("$this->baseUrl/Auth/RequestToken", $data);
    
        $response = $curl->response;
        
        $curl->close();
        if($response['status_code'] != 200)
            throw new Exception($response['status_message']);

        return $response['token'];
    
        
    }

   







   







 }
