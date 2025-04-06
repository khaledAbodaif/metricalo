
# Payment Processing Service

An API|CLI project built with PHP 8.0+ and Symfony 6.4  framework, supporting multiple payment gateways with including logging, event dispatching,  exception handling , httpClient Helper , Dto pattern , testing , deodorization .

## Key Features

-   **Multiple Payment Methods**: The API supports different payment methods, such as ACI , Shift4 and open for more, through a factory pattern that dynamically creates instances of payment services based on the provided method.

- **Command Line Interface (CLI):** The project includes a CLI command (StorePaymentCommand) that allows  to process payments directly from the command line or throw asking prompts.
- **Clean code**:  The project follows the principle of separation of concerns and SOLID by dividing responsibilities among different classes and services. Implementing event driven to make payment extendable .
## Getting Started


### Installation

#### Docker

1. Make sure Docker is installed
2. Run:
   ```bash
   git clone git@github.com:khaledAbodaif/metricalo.git
   cd metricalo
   docker-compose up -d
   docker-compose exec app composer install
   // for test cases
   docker-compose exec app ./vendor/bin/phpunit

3.  Access the application at  [http://localhost:8000](http://localhost:8000/)
#### Traditional
1. Make sure php dependencies installed and symfony
2. Run:
   ```bash
   git clone git@github.com:khaledAbodaif/metricalo.git
   cd metricalo
   composer install
   symfony server:start
      // for test cases
    ./vendor/bin/phpunit

3.  Access the application at  [http://localhost:8000](http://localhost:8000/)



## Usage

### API Endpoints


#### List Available Payment Methods

```json
GET /api/v1/payment/methods
```
**Response:**


```json

{
	"status": true,
	"message": "Data Retrieved Successfully",
	"data": [
		{
			"method": "shift4"
		},
		{
			"method": "aci"
		}
	]
}
```
#### Store Payment For Selected Method
```json 
POST /api/v1/payments/{method} shift4|aci
```
#### Request as json raw
```json
 {
  "amount": 100.0,
  "currency": "USD",
  "cardNumber": "4242424242424242",
  "cardExpMonth": "12",
  "cardExpYear": 2030,
  "cardCvv": "123"
}
```
**Success Response:**

```json
{
	"status": true,
	"message": "Payment processed successfully",
	"data": {
		"amount": 2,
		"currency": "USD",
		"transactionId": "char_yiYpU1i7tUK3tFdg9isUz2I9",
		"dateOfCreating": "2025-04-06T05:38:29+00:00",
		"cardBin": "424242"
		}
}
```

**Error Responses:**

```json

{
  "status": false,
  "message": "Payment failed!"
  "errors" : []
}
// case if payment sucessful and parseing error happen
{
  "status": "error",
  "message": "Payment failed! Contact the support",
  "errors" : []
}
// case if validation error
{
	"status": false,
	"message": "Validation Error",
	"errors": {
		"currency": "This value should not be blank."
	}
}
```


### Command Line Interface

Process payments directly from terminal:
```bash
bin/console app:store-payment {method} [payload]
```
**Example:**
```bash
bin/console app:store-payment aci '{"amount":92.00,"currency":"EUR","cardNumber":"4242424242424242","cardExpYear":2030,"cardExpMonth":"12","cardCvv":"123"}'
bin/console app:store-payment shift4
```
**Interactive Mode**  (when payload isn't provided there is asking prompts):

## Testing

Run the test suite with:

```json
bin/phpunit
```
#### Tests cover:
-   Payment processing logic
-   Different payment methods throw factory


## Database Note
The core implementation is designed to work without a database layer, focusing purely on API/CLI payment operations. However, the architecture is intentionally open for database integration when needed:

**Future Database Integration Points**:

    `Payment`  entity (morph table design)
    `Gateway`  entity for the methods need to be cached
    `Log` entity to save error logs 