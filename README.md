hyraiq/nz-companies-office-lookup
================

A PHP SDK to validate NZ Business Number (NZBNs) and verify them with the
[NZ Companies Office Public Data API](https://portal.api.business.govt.nz/api-details#api=nzbn).

The difference between validation and verification can be outlined as follows:

- Validation uses a regular expression to check that a given number is a valid NZBN. This _does not_ contact the API to
  ensure that the given ABN is assigned to a business
- Verification contacts the Companies Office through their API to retrieve information registered against the ABN. It
  will tell you if the ABN actually belongs to a business.

In order to use the API (only necessary for verification), you'll need to
[register an account](https://support.api.business.govt.nz/s/article/cloud-subscriptions) to receive an API key.


## Type safety

The SDK utilises the [Symfony Serializer](https://symfony.com/doc/current/components/serializer.html) and the
[Symfony Validator](https://symfony.com/doc/current/components/validator.html) to deserialize and validate data returned
from the API in order to provide a valid [NzCompanyResponse](./src/Model/NzCompanyResponse.php) model.
This means that if you receive a response from the SDK, it is guaranteed to be valid.

Invalid responses from the API fall into three categories, which are handled with exceptions:

- `ConnectionException.php`: Unable to connect to the API, or the API returned an unexpected response
- `NumberInvalidException.php`: The ABN is invalid (i.e. validation failed)
- `NumberNotFoundException.php`: The ABN is valid, however it is not assigned to a business (i.e. verification failed)


## Usage

### Installation

```shell
$ composer require hyraiq/nz-companies-office-lookup
```

### Configuration with Symfony

In `services.yaml`, you need to pass you ABR API key to the `ApiClient` and register the `ApiClient` with the
`ApiClientInterface`:

```yaml
Hyra\NzCompaniesHouseLookup\ApiClientInterface: '@Hyra\NzCompaniesHouseLookup\ApiClient'
Hyra\NzCompaniesHouseLookup\ApiClient:
    arguments:
        $apiKey: "%env(NZ_COMPANIES_OFFICE_API_KEY)%"
```

You can then inject the `ApiClientInterface` directly into your controllers/services.

```php
class VerifyController extends AbtractController
{
    public function __construct(
        private ApiClientInterface $apiClient,
    ) {
    }
    
    // ...  
}
```

You also need to add the custom address denormalizer to the `services.yaml`:

```yaml
Hyra\NzCompaniesOfficeLookup\Model\AddressDenormalizer: ~
```

### Configuration outside Symfony

If you're not using Symfony, you'll need to instantiate the API client yourself, which can be registered in your service
container or just used directly. We have provided some helpers in the `Dependencies` class in order to create the
Symfony Serializer and Validator with minimal options.

```php
use Hyra\NzCompaniesHouseLookup\Dependencies;
use Hyra\NzCompaniesHouseLookup\ApiClient;

$apiKey = '<insert your API key here>'

// Whichever http client you choose
$httpClient = new HttpClient();

$denormalizer = Dependencies::serializer();
$validator = Dependencies::validator();

$apiClient = new ApiClient($denormalizer, $validator, $httpClient, $apiKey);
```

### Looking up a business number

Once you have configured your `ApiClient` you can look up an individual ABN. Note, this will validate the ABN before
calling the API in order to prevent unnecessary API requests.

```php
$number = '9429032389470';

try {
    $response = $apiClient->lookupNumber($number);
} catch (ConnectionException $e) {
    die($e->getMessage())
} catch (NumberInvalidException) {
    die('Invalid business number');
} catch (NumberNotFoundException) {
    die('Business number not found');
}

echo $response->companyNumber; // 9429032389470
echo $response->entityName; // BURGER FUEL LIMITED
echo $response->status; // Registered
```


## Testing

In automated tests, you can replace the `ApiClient` with the `StubApiClient` in order to mock responses from the API.
There is also the `BusinessNumberFaker` which you can use during tests to get both valid and invalid NZBNs.

```php
use Hyra\NzCompaniesOfficeLookup\Stubs\BusinessNumberFaker;
use Hyra\NzCompaniesOfficeLookup\Stubs\StubApiClient;
use Hyra\NzCompaniesOfficeLookup\Stubs\MockBusinessRegistryResponse;

$stubClient = new StubApiClient();

$stubClient->lookupNumber(BusinessNumberFaker::invalidBusinessNumber()); // NumberInvalidException - Note, the stub still uses the validator

$stubClient->lookupNumber(BusinessNumberFaker::validBusinessNumber()); // LogicException - You need to tell the stub how to respond to specific queries

$businessNumber = BusinessNumberFaker::validBusinessNumber();
$stubClient->addNotFoundBusinessNumbers($businessNumber);
$stubClient->lookupNumber($businessNumber); // NumberNotFoundException

$businessNumber = BusinessNumberFaker::validBusinessNumber();
$mockResponse = MockBusinessRegistryResponse::valid();
$mockResponse->businessNumber = $businessNumber;

$stubClient->addMockResponse($mockResponse);
$response = $stubClient->lookupNumber($businessNumber); // $response === $mockResponse
```


## Contributing

All contributions are welcome! You'll need [docker](https://docs.docker.com/engine/install/) installed in order to
run tests and CI processes locally. These will also be run against your pull request with any failures added as
GitHub annotations in the Files view.

```shell
# First build the required docker container
$ docker compose build

# Then you can install composer dependencies
$ docker compose run php make vendor

# Now you can run tests and other tools
$ docker compose run php make (fix|psalm|phpstan|phpunit)
```

In order for you PR to be accepted, it will need to be covered by tests and be accepted by:

- [php-cs-fixer](https://github.com/FriendsOfPhp/PHP-CS-Fixer)
- [psalm](https://github.com/vimeo/psalm/)
- [phpstan](https://github.com/phpstan/phpstan)
