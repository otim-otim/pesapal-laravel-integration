<?php

namespace OtimOtim\PesapalIntegrationPackage\Services;

use Carbon\Carbon;
use Curl\Curl;
use Exception;
use Illuminate\Database\Eloquent\Model;
use OtimOtim\PesapalIntegrationPackage\Http\DTO\PaymentRequestDTO;
use OtimOtim\PesapalIntegrationPackage\Models\PesapalTransaction;

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


    public function sendRequest(Array $requestData, string $url, string $method = 'POST'){
        try {

            $curl = new Curl();
            $curl->setHeader('Accept', 'application/json');
            $curl->setHeader('Content-Type', 'application/json');    
            $curl->setHeader('bearer', $this->getAuthToken());    

            
            if($method == 'POST')
                $curl->post("$this->baseUrl/$url", $requestData);
            else
                $curl->get("$this->baseUrl/$url");
        
        
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


    public function initiatePayment(PaymentRequestDTO $dto, Model $user, Model $model){
        try {

            $response = $this->sendRequest($dto->toArray(), 'Transactions/SubmitOrderRequest');

            $response = json_decode($response, true);

            if($response['error'] )
                throw new Exception($response['message']);


            // create the database transaction record

            $transaction = new PesapalTransaction();
            $transaction->order_tracking_id = $response['order_tracking_id'];
            $transaction->merchant_reference = $response['merchant_reference'];
            $transaction->usable()->associate($user);
            $transaction->modelable()->associate($model);       
            $transaction->save();

            //return the redirect url
            return $response['redirect_url'];
             

        } catch (\Throwable $th) {
            throw $th;
        }

    }


    public function getTransactionStatus($order_tracking_id){
        try {
            $response = $this->sendRequest([], "Transactions/GetTransactionStatus?orderTrackingId=$order_tracking_id", 'GET');

            $response = json_decode($response, true);

            return $response;


        } catch (\Throwable $th) {
            throw $th;
        }
    }
   







   







 }
