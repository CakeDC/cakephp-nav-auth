# CakeDC Navision Authenticate plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require cakedc/cakephp-nav-auth
```

##Configuration

You need to configure the following settings using `Configure`:

```
Configure::write('NavAuth', [
    'url' => [
        //Protocol (http, https)
        'protocol' => 'your-protocol',
        //Server
        'server' => 'your-server',
        //Port
        'port' => 'your-port',
        //Instance
        'instance' => 'your-instance',
        //Method
        'method' => 'your-method',
        //Company String
        'company' => 'your-company',
        //Type
        'type' => 'your-type',
        //Endpoint
        'endpoint' => 'your-endpoint',
    ],
    'auth' => [
        // NTML authentication params
        'ntlm' => [
            'domain' => 'user-domain',
            'username' => 'username',
            'password' => 'user-password'
        ],
    ],
]);
```

## Usage

The plugin includes two authenticate objects: `Soap` and `OData`. To use any of them you can include the following code in your `AppController::initialize()`.
```
$this->loadComponent('Auth', [
    'authenticate' => [
        'CakeDC/NavAuth.Soap'
    ]
]);
```

Requirements
------------

* CakePHP 3.4.0+
* PHP 7.1+

Support
-------

For bugs and feature requests, please use the [issues](https://github.com/CakeDC/cakephp-nav-auth/issues) section of this repository.

Commercial support is also available, [contact us](https://www.cakedc.com/contact) for more information.


Contributing
------------

This repository follows the [CakeDC Plugin Standard](https://www.cakedc.com/plugin-standard). If you'd like to contribute new features, enhancements or bug fixes to the plugin, please read our [Contribution Guidelines](https://www.cakedc.com/contribution-guidelines) for detailed instructions.

License
-------

Copyright 2018 Cake Development Corporation (CakeDC). All rights reserved.

Licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) License. Redistributions of the source code included in this repository must retain the copyright notice found in each file.
