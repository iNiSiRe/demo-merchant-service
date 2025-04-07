# Merchant back-end implementation spec

## Overview
The goal of this implementation is to develop the Merchant back-end that allows the Client to:
- Retrieve their current balance
- Replenish the balance via an external PaymentGateway

## Components & Interactions

![Merchant<->PaymentGateway.png](Merchant%3C-%3EPaymentGateway.png)

### Participants:
- Client: Consumer (via Web Browser) of the Merchant service.
- Merchant: The system being implemented. Responsible for balance state and top-ups.
- PaymentGateway: External service responsible for processing financial transactions.

### Interactions flow

#### Balance Check
- Client sends request to the Merchant back-end to retrieve current balance.
- Merchant returns balance from persistent storage.

#### Replenishment Request
- Client sends a top-up request to the Merchant.
- Merchant back-end forwards this request to the PaymentGateway.
- After successful payment confirmation, Merchant updates balance and notifies Client about result.

## Functional requirements

### GET /balance
- Returns the current balance.
  
### POST /topUp
- Accepts amount and currency.
- Forwards the request to PaymentGateway.
- Handles response (success/failure).
- Updates user balance on success.
- Return payment processing result.

### Integration with PaymentGateway
- Creating a payment session and confirming the payment.
- Handle callbacks

## Data Storage
- User balance information must be stored in a persistent store (e.g., MySQL).
- Consider basic transaction locking and possible concurrency issues.

## Notes
- PaymentGateway is available locally as docker container (see docker-compose.yml)
- PaymentGateway API docs: [PaymentGatewayAPI.md](PaymentGatewayAPI.md)
