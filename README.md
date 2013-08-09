# Netifaces

### Installation

Add the following to your composer.json file

```json
{
    "requires": {
        "datasift/netifaces": "*"
    }
}
```

### Usage

We currently support two methods, `listAdapters()` and `getIpAddress()`.

```php
require __DIR__.'/../vendor/autoload.php';

// To get information about our network adapters, we need to know about two things
// 1. The OS that we're on
$os = Datasift\Os::getOs();
// 2. We need a parser for the ifconfig output
$parser = Datasift\IfconfigParser::fromDistributions($os->getPossibleClassNames());

// Next, we create a new netifaces instance, passing in our OS and Parser
$netifaces = new Datasift\netifaces($os, $parser);

// Then we can list the available adapters
var_dump($netifaces->listAdapters());

// Or get the IP address if a specific adapter
var_dump($netifaces->getIpAddress("eth0"));
```
