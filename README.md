# php-ip
An object implementation of the ip addresses and their parsing

The installation of this library is made via composer.
Download `composer.phar` from [their website](https://getcomposer.org/download/).
Then add to your composer.json :

```json
	"require": {
		...
		"php-extended/php-ip": "^1",
		...
	}
```
Then run `php composer.phar update` to install this library.
The autoloading of all classes of this library is made through composer's autoloader.

## Basic Usage

You may use this library this following way:

```php

use PhpExtended\Ip\Ipv4;
use PhpExtended\Ip\ParseException;

$addr = '<put your ip address here>'; 	// "255.255.255.255" format
try
{
	$ipv4 = new Ipv4($addr);
}
catch(ParseException $e)
{
	// does something
}
```

The same process is usable with the `Ipv6` class, for version 6 of IP
protocol. This library provides also network classes to be able to evaluate
if a specific ip address is within a network.

For example, if you want to ask if an Ipv4 is within the 10.0.0.* network, 
just do the following :

```php

$is_in_range = $ipv4->isInRange('10.0.0.0/24');

```

The `'10.0.0.0/24'` string will be parsed as a network with 255 adresses
available and the 10.0.0.0 base network address.

## License

MIT (See [license file](LICENSE)).
