# Commission Fee Calculator

An experimental Dev-Task that parses operations from a csv file and calculates commission fees.

## Installation

Clone repository and install dependencies via composer.

     composer install --no-dev  
## Usage

```php  
<?php

use Payme\CommissionFeeCalculator\OperationCollection;
use Payme\CommissionFeeCalculator\Services\CurrencyService;
use Payme\CommissionFeeCalculator\Services\CommissionService;

$currencies = [
    [
        'symbol'    => 'EUR',
        'rate'      => 1,
        'precision' => 2,
    ],
    [
        'symbol'    => 'USD',
        'rate'      => 1.129031,
        'precision' => 2,
    ],
    [
        'symbol'    => 'JPY',
        'rate'      => 129.53,
        'precision' => 0,
    ]
];

$currencyService = new CurrencyService();
$currencyService->collectCurrenciesFromArray($currencies);
$collection = new OperationCollection();

try {
    $collection->parseFromCSV($argv[1]);
} catch (Exception $e) {
}

$commissionService = new CommissionService($currencyService);
foreach ($commissionService->calculateFeesFromCollection($collection) as $fee) {
    echo $fee . PHP_EOL;
}
```
## Demo

     php main.php input.csv

## Tests

Install dev dependencies via composer and run tests.

     composer install --dev
     ./vendor/bin/phpunit tests
