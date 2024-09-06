<?php

namespace OtimOtim\PesapalIntegrationPackage\Http\DTO;

use InvalidArgumentException;

class BillingAddressDTO
{
    public ?string $email_address;
    public ?string $phone_number;
    public ?string $country_code;
    public ?string $first_name;
    public ?string $middle_name;
    public ?string $last_name;
    public ?string $line_1;
    public ?string $line_2;
    public ?string $city;
    public ?string $state;
    public ?string $postal_code;
    public ?string $zip_code;

    public function __construct(array $data)
    {
        // Ensure either email_address or phone_number is provided
        if (empty($data['email_address']) && empty($data['phone_number'])) {
            throw new InvalidArgumentException("Either email_address or phone_number must be provided.");
        }

        $this->email_address = $data['email_address'] ?? null;
        $this->phone_number = $data['phone_number'] ?? null;
        $this->country_code = $data['country_code'] ?? null;
        $this->first_name = $data['first_name'] ?? null;
        $this->middle_name = $data['middle_name'] ?? null;
        $this->last_name = $data['last_name'] ?? null;
        $this->line_1 = $data['line_1'] ?? null;
        $this->line_2 = $data['line_2'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->state = $data['state'] ?? null;
        $this->postal_code = $data['postal_code'] ?? null;
        $this->zip_code = $data['zip_code'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'email_address' => $this->email_address,
            'phone_number' => $this->phone_number,
            'country_code' => $this->country_code,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'line_1' => $this->line_1,
            'line_2' => $this->line_2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'zip_code' => $this->zip_code,
        ];
    }
}