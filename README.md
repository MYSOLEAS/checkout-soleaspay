# SoleasPay Checkout Page

The SoleasPay payment aggregator's checkout page allows your customers to securely pay for goods or services online by combining multiple payment methods.

## Support us

[<img src="https://app.soleaspay.com/images/sopay.png" width="200px" />](https://soleaspay.com)

We invest a significant amount of resources in creating plugins to facilitate the integration of online payments via our SoleasPay platform. You can support us by using our checkout page on your application.


## How it Works

When the user accesses the Checkout page, they can view the transaction details, such as the amount to be paid, the description, and the order ID.

They are prompted to choose a service from the list of services if you have not defined the service to use among the services in this list. They will then need to enter their phone number for Orange Money CM, MTN Mobile Money CM, and Express Union services, for the PayPal service, you must enter your PayPal email and for cryptocurrencies we will provide you with a wallet to make the transfer

To complete the operation, they must click the confirm button to execute the transaction. If the transaction is successful, the user is redirected to a payment confirmation page. Otherwise, they are redirected to an error page.

## Usage

You should send the following information in the request body to the URL https://checkout.soleaspay.com/ :  
* __apiKey__ : This is the API key of the [Soleaspay](https://soleaspay.com) business account that will receive the payment. It can be obtained either from the professional account dashboard or by requesting it via email at support@mysoleas.com ;
* __service__ : If you want the user/customer to choose a service from the list of services(orange_money_CM, mtn_mobile_money_CM,  paypal, express_union, perfect_money, visa, master, mastercard, bitcoin, litecoin, dogecoin), you should not send a service in your request. However, if you want to define a single service that the user/customer must use, you should send the name of one of our services as mentioned in the list above.
* __amount__ : This is the amount that the user/customer must pay for an operation.  
* __currency__ (XAF, USD, EUR) : This is the currency in which the payment must be made. It must match the default currency of the service selected by the user/customer. 
* __orderId__ : This is the unique payment reference in the partner's system.
* __description__ : this is the description or purpose of the initiated payment;
* __shopName__ : The name of your business.
* __successUrl__ : This is the URL to which the user/customer will be redirected in case of a successful operation.
* __failureUrl__ : This is the URL to which the user/customer will be redirected in case of a failed operation.

Here is an example of how to send information in Laravel : 

```php
use Illuminate\Support\Facades\Http;

// Create an array containing the parameters to be sent
$params = [
    'apiKey' => 'sjnfb$uGJV23423sj-sadh ',
    'amount' => '20000',
    'currency' => 'XAF',
    'description' => 'Bill payment',
    'orderId' => 'GBV43A53WD',
    'service' => 'orange_money_CM',
    'shopName' => 'MYSOLEAS',
    'successUrl' => 'http://exemple.com/success',
    'failureUrl' => 'http://exemple.com/failure'
];

// Send a POST request to the URL
$response = Http::post('https://checkout.soleaspay.com/', $params);

```

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mysoleas](https://mysoleas.com)
- [Soleaspay](https://soleaspay.com)


