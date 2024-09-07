<?php

namespace OtimOtim\PesapalIntegrationPackage\Tests;

use Mockery;
use Curl\Curl;
use Orchestra\Testbench\TestCase;
use Illuminate\Database\Eloquent\Model;
use OtimOtim\PesapalIntegrationPackage\Services\PesapalService;
use OtimOtim\PesapalIntegrationPackage\Models\PesapalTransaction;
use OtimOtim\PesapalIntegrationPackage\Http\DTO\PaymentRequestDTO;
use OtimOtim\PesapalIntegrationPackage\Enums\TransactionStatusEnum;
use OtimOtim\PesapalIntegrationPackage\PesapalIntegrationPackageServiceProvider;

class PesapalServiceTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [PesapalIntegrationPackageServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Set up any additional initializations if necessary
    }

    protected function mockCurlResponse(array $response, int $statusCode = 200)
{
    $curlMock = Mockery::mock(Curl::class);
    $curlMock->shouldReceive('setHeader');
    $curlMock->shouldReceive('post')
             ->andReturnSelf();
    $curlMock->shouldReceive('get')
             ->andReturnSelf();
    $curlMock->response = $response;
    
    return $curlMock;
}


    /** @test */
public function it_can_get_auth_token()
{
    $pesapalService = Mockery::mock(PesapalService::class)->makePartial();
    
    $mockResponse = [
        'status_code' => 200,
        'token' => 'dummy-token',
        'expiryDate' => now()->addMinutes(10)->toString(),
    ];

    $curlMock = $this->mockCurlResponse($mockResponse);
    $pesapalService->shouldReceive('getAuthToken')->passthru()->once();
    $this->app->instance(Curl::class, $curlMock);

    $token = $pesapalService->getAuthToken();
    
    $this->assertEquals('dummy-token', $token);
}


    /** @test */
public function it_can_send_post_request()
{
    $pesapalService = Mockery::mock(PesapalService::class)->makePartial();

    $mockResponse = [
        'status_code' => 200,
        'message' => 'Request successful'
    ];

    $curlMock = $this->mockCurlResponse($mockResponse);
    $this->app->instance(Curl::class, $curlMock);

    $response = $pesapalService->sendRequest(['key' => 'value'], 'dummy-url');

    $this->assertEquals($mockResponse, $response);
}


    /** @test */
public function it_can_initiate_payment_and_store_transaction()
{
    $user = Model::factory()->create();
    $model = Model::factory()->create();

    $dto = new PaymentRequestDTO(/* fill required parameters */);

    $pesapalService = Mockery::mock(PesapalService::class)->makePartial();

    $mockResponse = [
        'order_tracking_id' => 'order123',
        'merchant_reference' => 'merchant123',
        'redirect_url' => 'https://pesapal.com/redirect',
        'error' => false
    ];

    $curlMock = $this->mockCurlResponse($mockResponse);
    $this->app->instance(Curl::class, $curlMock);

    $redirectUrl = $pesapalService->initiatePayment($dto, $user, $model);

    $this->assertEquals('https://pesapal.com/redirect', $redirectUrl);
    $this->assertDatabaseHas('pesapal_transactions', [
        'order_tracking_id' => 'order123',
        'merchant_reference' => 'merchant123',
    ]);
}

    /** @test */
public function it_can_update_transaction_status()
{
    $pesapalService = Mockery::mock(PesapalService::class)->makePartial();

    $mockResponse = [
        'status_code' => 1, // COMPLETED
        'amount' => 1000,
        'currency' => 'USD',
        'payment_method' => 'credit_card'
    ];

    $curlMock = $this->mockCurlResponse($mockResponse);
    $this->app->instance(Curl::class, $curlMock);

    $transaction = PesapalTransaction::factory()->create([
        'order_tracking_id' => 'order123',
    ]);

    $updatedTransaction = $pesapalService->updateTransactionStatus('order123');

    $this->assertEquals(TransactionStatusEnum::COMPLETED, $updatedTransaction->status);
    $this->assertEquals(1000, $updatedTransaction->amount);
    $this->assertEquals('USD', $updatedTransaction->currency);
    $this->assertEquals('credit_card', $updatedTransaction->payment_method);
}

    /** @test */
public function it_can_cancel_payment_request()
{
    $pesapalService = Mockery::mock(PesapalService::class)->makePartial();

    $mockResponse = [
        'status' => 200,
        'message' => 'Cancelled successfully'
    ];

    $curlMock = $this->mockCurlResponse($mockResponse);
    $this->app->instance(Curl::class, $curlMock);

    $transaction = PesapalTransaction::factory()->create([
        'order_tracking_id' => 'order123',
        'status' => TransactionStatusEnum::COMPLETED,
    ]);

    $updatedTransaction = $pesapalService->cancelPaymentRequest('order123');

    $this->assertEquals(TransactionStatusEnum::CANCELLED, $updatedTransaction->status);
}

}
