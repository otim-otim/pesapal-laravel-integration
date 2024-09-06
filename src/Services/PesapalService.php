<?php

namespace OtimOtim\PesapalIntegrationPackage\Services;

use Curl\Curl;
use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OtimOtim\PesapalIntegrationPackage\Models\PesapalTransaction;
use OtimOtim\PesapalIntegrationPackage\Http\DTO\PaymentRequestDTO;
use OtimOtim\PesapalIntegrationPackage\Enums\TransactionStatusEnum;

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


    public function getTransactionDetails($order_tracking_id){
        try {
            $response = $this->sendRequest([], "Transactions/GetTransactionStatus?orderTrackingId=$order_tracking_id", 'GET');

            $response = json_decode($response, true);

            if($response['error'] )
                throw new Exception($response['error']['message']);

            return $response;


        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateTransactionStatus($order_tracking_id){
        try {
            $details = $this->getTransactionDetails($order_tracking_id);
            $transaction = PesapalTransaction::where('order_tracking_id', $order_tracking_id)->firstOrFail();
            $transaction->status =  match($details['status_code']) {
                0 => TransactionStatusEnum::INVALID,
                1 => TransactionStatusEnum::COMPLETED,
                2 => TransactionStatusEnum::FAILED,
                3 => TransactionStatusEnum::REVERSED
                //todo: default status code
            };
            $transaction->amount = $details['amount'];
            $transaction->currency = $details['currency'];
            $transaction->payment_method = $details['payment_method'];
            $transaction->save();
            return $transaction;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function cancelPaymentRequest($order_tracking_id){
        try {
            $response = $this->sendRequest(['order_tracking_id' => $order_tracking_id], "Transactions/CancelOrder");
            
            $response = json_decode($response, true);

            if($response['status'] == 200 ){
                // update transaction status

                $transaction = PesapalTransaction::where('order_tracking_id', $order_tracking_id)->firstOrFail();
                $transaction->status =  TransactionStatusEnum::CANCELLED;
                $transaction->save();
                return $transaction;
            }

            else 
                throw new Exception($response['message'], 500);


        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //TODO: reverse transaction
    //todo: set recurring payments
   







   







 }
