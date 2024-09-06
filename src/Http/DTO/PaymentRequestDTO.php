<?php

namespace OtimOtim\PesapalIntegrationPackage\Http\DTO;

use Illuminate\Http\Request;
class PaymentRequestDTO
{
    public string $id;
    public string $currency;
    public float $amount;
    public string $description;
    public string $callback_url;
    public string $notification_id = config('PesapalIntegrationPackage.NOTIFICATION_ID');
    public BillingAddressDTO $billing_address;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->currency = $data['currency'];
        $this->amount = $data['amount'];
        $this->description = $data['description'];
        $this->callback_url = $data['callback_url'];
        $this->billing_address = new BillingAddressDTO($data['billing_address']);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'description' => $this->description,
            'callback_url' => $this->callback_url,
            'notification_id' => $this->notification_id,
            'billing_address' => $this->billing_address->toArray(),
        ];
    }

    public static function fromRequest(Request $request): self
    {
        return new self([
            'id' => $request->input('id'),
            'currency' => $request->input('currency'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
            'callback_url' => $request->input('callback_url') ?? config('PesapalIntegrationPackage.CALLBACK_URL')  ,
            'notification_id' => $request->input('notification_id') ?? config('PesapalIntegrationPackage.NOTIFICATION_ID'),
            'billing_address' => [
                'email_address' => $request->input('billing_address.email_address'),
                'phone_number' => $request->input('billing_address.phone_number'),
                'country_code' => $request->input('billing_address.country_code'),
                'first_name' => $request->input('billing_address.first_name'),
                'middle_name' => $request->input('billing_address.middle_name'),
                'last_name' => $request->input('billing_address.last_name'),
                'line_1' => $request->input('billing_address.line_1'),
                'line_2' => $request->input('billing_address.line_2'),
                'city' => $request->input('billing_address.city'),
                'state' => $request->input('billing_address.state'),
                'postal_code' => $request->input('billing_address.postal_code'),
                'zip_code' => $request->input('billing_address.zip_code'),
            ]
        ]);
    }
}