# Erply API request handler
## Requirements
> PHP >= 5.6

## Erply API Docs
* [Erply InventoryAPI](https://learn-api.erply.com/)
* [List of API requests](https://learn-api.erply.com/requests)
* [Limits](https://learn-api.erply.com/getting-started/limits)

## Usage
```php
<?php
require('vendor/autoload.php');
use NewtimeEst\ErplyApi\ErplyApi;

$erplyApi = new ErplyApi('<username>', '<password>', '<clientCode>');

$erplyProducts = $erplyApi->request('getProducts'));
```
