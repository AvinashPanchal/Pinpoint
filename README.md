# Pinpoint
wordpress pinpoint 3DS integration
#3DS Integrator

This SDK (3DS Integrator) can be used to add risk based authentication to online shopping carts. The SDK follows the same workflow provided in the mpi documentation. 

## Requirements
* PHP 5.6 or higher
* PHP Curl Extension

## Quick start
1. Create an integration script with the following code. You can use composer to install the SDK or use the provided `autoload.php`. The code below uses the `autoload.php`. Be sure to update the script with the appropriate configuration options (see the [configuration section](#configuration) for options)
```php
<?php
include_once 'path/to/sdk/autoload.php';
$config = new \ThreeDS\Integrator\Config('api_key','api_secret');
$config->setDemo(true);
$config->setTimeout(12);


$api = new \ThreeDS\Integrator\Api\Adapter\Curl();
$payment = new \ThreeDS\Integrator\Request\PaymentRequest($_POST);
$responseHandler = new \ThreeDS\Integrator\Response\VerificationResponse();
$integrator = new \ThreeDS\Integrator\Integrator($config,$api,$payment,$responseHandler);

;?>

<?php echo $integrator->render();?>
```
2. Add the following fields to the payment form (alternatively you create a custom `AbstractPaymentRequest`. See the [Custom Payment Form](#custom-payment-form) for more details)
    * `x_amount` - This is the billing amount
    * `x_exp_month` - This is the card expiration month (e.g. 01)
    * `x_exp_year` - This is the card expiration year (e.g. 16)
    * `x_card_num` - This is the card no. 
    * `x_relay_url` - This is the url to eventually submit the results to

3. The `x_relay_url` end point that will receive the results of the 3dsecure verification should be setup to receive the results and then submit the payment via a payment gateway
4. Update the payment form to submit to the integration script created

See the examples folder for code samples

## How It Works
The billing information is submitted to the integration page. The integration page which makes the necessary calls to check if the user is enrolled, and verifies the user. 

The billing information that was entered is then submitted to the endpoint specified in the `x_relay_url` parameter of the form along with the `eci` and `cavv` if the verification was successful or a `3ds_secure_error` if it was not. In either case the `x_relay_url` is where the payment should be submitted for processing to the gateway.

## Configuration 
There are a number of configuration options available to help the sdk fit integration needs

### Config Parameters
On the `ThreeDS\Integrator\Config` class there are a few options available:


|      Method      | Default |                                                                                               Description                                                                                               | Example                                                         |
|:----------------:|:-------:|:-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|-----------------------------------------------------------------|
| setDemo          | false   | Toggle sandbox mode on and off                                                                                                                                                                          | $config->setDemo(true)                                          |
| setTimeOut       | 10      | How long to wait for the challenge in the iframe to return results  before moving on with payment processing                                                                                            | $config->setTimeOut(10)                                         |
| setHideForm      | true    | In some instances users are prompted with a challenge. This option hides the challenge form. After the configured timeout the payment will be forwarded to the processing form without 3DS verification | $config->setHideForm(false)                                     |
| setIntegratorUrl |         | This is the url to the integration page that was setup. By default the config tries to construct it                                                                                                     | $config->setIntegratorUrl('http://example.com/integration.php') |

## Custom Payment Form
Custom named inputs on the payment form can also be used; the names of the fields just need to be mapped in the integrator by creating a custom `AbstractPaymentRequest` e.g.

```
class MyPaymentRequest extends \ThreeDS\Integrator\Request\AbstractPaymentRequest
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getAmount() {
        return 1;
    }
    public function getCardNumber() {
        return $this->getValue('ccNum');
    }
    public function getExpirationMonth() {
        return substr($this->getValue('expDate'),0,2);
    }
    public function getExpirationYear() {
        return substr($this->getValue('expDate'),-2);
    }
    public function getTransactionId() {
        return $this->getValue('x_transaction_id');
    }
    public function getMessageId() {
        return $this->getValue('x_message_id');
    }

    public function getRelayUrl()
    {
        return $this->getValue('x_relay_url');
    }

}
```

Note that the get methods MUST be implemented. `getValue` can be used to retrieve data from the form post e.g. if the billing form has an input named `expDate` then it can be retrieved using `$this->getValue('expDate');`

For a full example of a custom payment form see the examples/custom folder. 
