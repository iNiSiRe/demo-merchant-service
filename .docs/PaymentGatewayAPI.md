# Requests (Merchant/Client -> PaymentGateway)

## Overview

| Name                        | API                                    | Request Format                           | Response Format                    | Comment                                                         |
|-----------------------------|----------------------------------------|------------------------------------------|------------------------------------|-----------------------------------------------------------------|
| Create **Client**'s payload | GET http://gateway/api/authlink/obtain | **QueryParams\<PaymentPayloadRequest\>** | **Json\<PaymentPayloadResponse\>** | Ask **PaymentGateway** to create payload for **Client** request |
| Open payment page           | GET http://gateway/api/open            | **QueryParams\<OpenRequest\>**           | HTML                               | Ask **PaymentGateway** to show payment processing page          |

## Format types definitions

### PaymentPayloadRequest

| Name          | Type   | Comment                                                |
|---------------|--------|--------------------------------------------------------|
| site_id       | int    | **Merchant** identifier provided by **PaymentGateway** |
| site_login    | int    | **Client** identifier provided by **Merchant**         |
| amount_series | ?float | Recommended sum                                        |
| currency      | string | Currency ISO 4217 code (USD, EUR)                      |
| force_action  | int    | Must be '1', for compatibility purpose                 |

### PaymentPayloadResponse

| Name      | Type   | Comment         |
|-----------|--------|-----------------|
| success   | bool   |                 |
| key       | string | Payment payload |
| signature | string | May not be used |

### OpenRequest

| Name | Type   | Comment                                                             |
|------|--------|---------------------------------------------------------------------|
| key  | string | Payment payload provided by **PaymentGate** on /api/authlink/obtain |

# Callbacks (PaymentGateway -> Merchant)

## Overview

| Name          | API                                    | Request Format              | Response Format                             | Comment                                                                                         |
|---------------|----------------------------------------|-----------------------------|---------------------------------------------|-------------------------------------------------------------------------------------------------|
| check_deposit | POST <CALLBACK_GATE_URL>/cashbox_request | **Json\<CheckDeposit\>**    | **Json\<CheckDepositResult\>**              | **PaymentGateway** ask **Merchant** about payment initialization for **Client**                 |
| callback      | POST <CALLBACK_GATE_URL>/callback      | **Json\<PaymentCallback\>** | **Json\<PaymentCallbackProcessingResult\>** | **PaymentGateway** notify **Merchant** about payment processing result. **MUST BE IDEMPOTENT**. |

## Format types definitions

### CheckDeposit

| Name                     | Type   | Comment                                                                                                             |
|--------------------------|--------|---------------------------------------------------------------------------------------------------------------------|
| name                     | string | Action name                                                                                                         |
| site_login               | int    | **Client** identifier provided by **Merchant**                                                                      |
| amount                   | int    | Transaction sum in **minor** units (<a href="https://en.wikipedia.org/wiki/ISO_4217#Minor_unit_fractions">wiki</a>) |
| currency                 | string | Currency ISO 4217 code (e.g. USD, EUR)                                                                              |
| payment_group_id         | int    | May not be used                                                                                                     |
| display_payment_group_id | int    | May not be used                                                                                                     |
| payment_type_id          | int    | May not be used                                                                                                     |

#### Example

```json
{
  "name": "check_deposit",
  "site_login": 1,
  "amount": 1300,
  "currency": "USD",
  "payment_group_id": 1,
  "display_payment_group_id": 1,
  "payment_type_id": 0
}
```

### CheckDepositResult

| Name        | Type | Comment                                                      |
|-------------|------|--------------------------------------------------------------|
| code        | int  | 0 - success, other - error                                   |
| external_id | int  | **Client**'s transaction identifier provided by **Merchant** |

#### Example

```json
{
  "code": 0,
  "external_id": 1
}
```

### PaymentCallback

| Name              | Type   | Comment                                                                                                             |
|-------------------|--------|---------------------------------------------------------------------------------------------------------------------|
| currency          | string | Currency ISO 4217 code (e.g. USD, EUR)                                                                              |
| amount            | int    | Transaction sum in **minor** units (<a href="https://en.wikipedia.org/wiki/ISO_4217#Minor_unit_fractions">wiki</a>) |
| external_id       | int    | **Client**'s transaction identifier provided by **Merchant**                                                        |
| status_id         | int    | Status identifier                                                                                                   |
| customer_purse    | string | Customer's purse/account number                                                                                     |
| bank_country      | string | Bank's country name                                                                                                 |
| processor_message | string | Message from the payment processor                                                                                  |
| signature         | string | Signature (see **Signature generation/verification**)                                                               |

#### Example

```json
{
  "currency": "USD",
  "amount": 1300,
  "external_id": 1,
  "status_id": 4,
  "customer_purse": "5555555555554444",
  "bank_country": "UKRAINE",
  "processor_message": ""
}
```

### PaymentCallbackProcessingResult

| Name | Type | Comment                    |
|------|------|----------------------------|
| code | int  | 0 - success, other - error |

#### Example

```json
{
  "code": 0
}
```

## Signature verification/generation

Signature of **PaymentCallback** must be verified.

### Signature generation algorithm (PHP code)

```php
function createSignature(array $data, string $secretKey) {
    unset($data['signature']);
    
    $raw = '';
    ksort($data);
    foreach ($data as $key => $value) {
        if ($value === '' || $value === null) {
            continue;
        }
        $raw .= $key . ':' . $value . ';';
    }
    
    return sha1($raw . $secretKey);
}
```

### Verification example

```php
$callback = ... // Received from PaymentGateway callback request
$secretKey = ... // Secret key provided by PaymentGateway for Merchant

$signatureValid = $callback['signature'] === createSignature($callback, $secretKey);

if ($signatureValid) {
    // continue callback processing
} else {
    // reject, return error
}  
```

## Failed payment mocking
PaymentGateway can simulate the behavior of a failed payment by using 
a special bank card configured via an environment variable.

## Docker container configuration

### Docker image
```shell
inisire/payment-gateway-mock:latest
```

### Environment variables

| Name                   | Example                                   | Comment                                          |
|------------------------|-------------------------------------------|--------------------------------------------------|
| SIGNATURE_KEY          | abcdef12345                               | Secret key using by signature generation         | 
| CALLBACK_GATE_URL      | http://merchant.com/api/payments/callback | Merchant's endpoint for callbacks                |
| CALLBACK_SITE_ID       | 1                                         | Merchant's ID                                    |
| SUCCESS_CARDS          | '["5555555555554444","5555555555553333"]' | Bank card numbers behave like success            |
| NOT_ENOUGH_FUNDS_CARDS | '["1111111111111111"]'                    | Bank cards number behave like 'Not enough funds' |
