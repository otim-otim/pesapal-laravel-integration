<?php

namespace OtimOtim\PesapalIntegrationPackage\Services;

use Carbon\Carbon;
use Curl\Curl;
use Exception;
use OtimOtim\PesapalIntegrationPackage\Http\DTO\PaymentRequestDTO;

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

    public function makePayment(){
        try {
            
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

   

    private function getAuthToken(){
        try {

            if(!$this->isExpired())
                return $this->token;

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

            $this->token = $response['token'];
            $this->expiry = $response['expiryDate'];
    
            return $response['token'];
        } catch (\Throwable $th) {
            throw $th;
        }
    
        
    }


    public function sendRequest(Array $requestData, string $url){
        try {

            $curl = new Curl();
            $curl->setHeader('Accept', 'application/json');
            $curl->setHeader('Content-Type', 'application/json');    
            $curl->setHeader('bearer', $this->getAuthToken());    

            
    
        
            $curl->post("$this->baseUrl/$url", $requestData);
        
            $response = $curl->response;
            
            $curl->close();

            return $response;
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public  function getPesapalNotificationId(){
        try {

            $data = array(
                'url' => config('PesapalIntegrationPackage.NOTIFICATION_URL') ,
                'ipn_notification_type' => 'POST'
            );
            $url = 'URLSetup/RegisterIPN';

            $response = $this->sendRequest($data, $url );

            //todo: add notification id to env
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function initiatePayment(PaymentRequestDTO $dto){
        try {

            $response = $this->sendRequest($dto->toArray(), 'Transactions/SubmitOrderRequest');

            $response = json_decode($response, true);

            if($response['error'] )
                throw new Exception($response['error']);


            //todo create the databse record

            return $response;
             

        } catch (\Throwable $th) {
            throw $th;
        }

    }
   







   







 }
