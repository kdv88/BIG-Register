# BIG Registration Utils

## Introduction
A quick, hacked up connector to the Dutch government's  [BIG-register system](https://www.bigregister.nl/zoeken/zoeken_eigen_systeem/), an online list of health care professionals. This is a proof-of-concept. Mostly.

NuSOAP is used because the BIG-Register system triggers a particular issue in PHP's native SOAPClient, [causing many headaches](https://bugs.php.net/bug.php?id=60842). 

This package is based on the work of [rosstuck/BIG-Register](https://github.com/rosstuck/BIG-Register).

NuSOAP is licensed under LGPL.
All other code is licensed as MIT.

## Installation

```shell
$ composer require kdv/big-register:dev-master
```

## Usage

```php
require_once __DIR__ . '/vendor/autoload.php';

use Kdv\BigRegister\BigRepository;

$repo = new BIGRepository();
```
