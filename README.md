# VSD

PHP-client-library for vs code extension VSDump

remember to setup VSD_HOST in your .env file
default host is host.docker.internal, post is 9913 and cant be changed.

if running inside a docker container, make sure to add "host.docker.internal:host-gateway" to your extra_hosts
to be able to access the host machine.
## Installation
```bash
composer require hugoup/vsd-php
```


## Usage

```php
vsd($variable)
```

or 

```php
vsd($variable1,$variable2,$variable3)
```


