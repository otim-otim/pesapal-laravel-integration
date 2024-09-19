# **Pesapal Laravel Integration**

This package integrates the Pesapal v3 api with Laravel. It is currently limited to the Pesapal e-commerce API even though there are future plans to add the POS API as well.
The package comes with tests written to make the package gurrantee a certain level reliability and predictability.

## **Requirements**

1. PHP >= 8.1 
2. Laravel 10 or later
3. Pesapal Account

## **Installation**

To install the package, we nedd to require it using composer.

        composer require otim-otim/pesapal-laravel-integration

## **Configuration**

At this point, its necessary to publish the config file for the package into the main project. Therefore we run:

        php artisan vendor:publish --provider="OtimOtim\PesapalIntegrationPackage\PesapalIntegrationPackageServiceProvider" --tag="config"

This will add the PesapalIntegrationPackage.php file into the config folder of your main project.
The contents of this file will be as below:-

```
<?php

return [
  "CONSUMER_KEY" => "", // enter your consumer key here

  "CONSUMER_SECRET" => "", //enter your consumer secret here

  'SAND_BOX_URL' => 'https://cybqa.pesapal.com/pesapalv3/api', //enter sandbox url here
  'LIVE_URL'  => 'https://pay.pesapal.com/v3/api', //enter live url here


  /*
  get the notification id from pesapal
  and add here.
  check pesapal documentation for more details
  */
  'NOTIFICATION_ID' => '', //notification id from pesapal
  /*
  url pesapal will hit this route whenever action is done to a payment request
  NB:be sure to create this route
  for our case, this will be a post route
  */
  'NOTIFICATION_URL' => '', 

  /*
  url pesapal will redirect your user to after processing payment 
  NB:be sure to create this route
  */
  'CALLBACK_URL' => '', 
  /*
  url pesapal will redirect your user to after canceling payment 
  NB:be sure to create this route, if you want to enable payemnt request cancelation.
  check pesapal documentation for more details
  */
  'CANCELLATION_URL' => '', 


];

```

At this point, it's important to set up your notification url route which receives a post request.

Next, we need to obtain the notification ID from pesapal, via the this url:-

[get pesapal sandbox notification ID](https://cybqa.pesapal.com/PesapalIframe/PesapalIframe3/IpnRegistration)

[get pesapal Live account notification ID](https://pay.pesapal.com/iframe/PesapalIframe3/IpnRegistration)

While the cancellation url is optional, the rest of the properties require values. 
With the callback url being the default, but can be switched to your application preference, by passing a callback url request value to the request object.(more on this later).


Now we are ready to run our migrations.

    php artisan migrate

## **Usage**

To use the package, all we need to do is import the package class to the file from which we need to access its functionality, like so:-

`use OtimOtim\PesapalIntegrationPackage\Facades\Pesapal;`

1. ### **Preparing the order request**

To prepare the order request, its important send data using the PaymentRequestDTO dto class, which will also need to be imported at the top of your file.

`use OtimOtim\PesapalIntegrationPackage\Http\DTO\PaymentRequestDTO;`

The payment Request constructor expects the following values in the passed in array:-

```
 $data = [
            'id' => '657-dfetr-iuy', //a uuid is prefered, but you can use your unique string to identify the transaction from your merchant side.
            "amount" => 2000,
            "currency" => 'UGX',// use standard currency code
            "description" => 'making a payment',
            "reference" => Uuid::uuid4()->toString(),
            'call_back_url' => route('payment.show', $payment), //this is optional, and can be set in case you want to overide the default CALLBACK_URL from the config file above. 
            'billing_address' => [ // atleast email address or phone_number should be provided. Otherwise validation will fail.
                'email_address' => $user->email,
                'phone_number' => $user->phone_number,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
            ]

        ];

        $dto = new PaymentRequestDTO($data);
```

2. ### **Initiating the payment**

With our Data Transfer Object(dto) ready, we are ready to initiate the payment with paypal.

` $pay_url = Pesapal::initiatePayment($dto, $user , $item);`

The initiatePayemnt method takes 3 arguments:-
1. The dto(containg the data for processing the request).
2. The user to which this payment should be attached. it should be a laravel model.
3. The item model for which we are making the payment.

The method, if successful, returns a redirect url, which takes the user to the pesapal interface, to complete the transaction.

3. ### **Checking Transaction Status**

you can check the status of a transaction at any point, using the getTransactionDetails() method.

`$transaction = Pesapal::getTransactionDetails($merchant_reference);`

The $merchant_reference is the merchant reference you assigned to the id array value  above.

4. ### **Updating Transaction Details in the database**

you can call the updateTransactionStatus() to update the transaction status in your own database.

`$transaction = Pesapal::updateTransactionStatus($merchant_reference);`


5. ### **Canceling a Payment Request**

Only a payment that hasn't completed can be cancelled.
you can do this by calling the cancelPaymentRequest() method.

`$transaction = cancelPaymentRequest($order_tracking_id)`

where :-
    order_tracking_id : the unique order tracking id provided by pesapal.
it return a transaction model if successful, else an error will be thrown. so be sure to catch it.

You can take advantage of the steps 3 and 4 above normally when you receive a notification from pesapal via your notification url route set in the config file.





## **Next Steps**

It's clear, the package is still a work in progress, as reversing a transaction and setting recurring payments are still underway.
I hope to add the POS api as well in the next release.

This project licensed with an MIT license, so dont be afraid to fork and make that PR, or point out any issues or points for improvement.

If you used this package, please also dont forget to leave a comment or review. i will really appreciate.
