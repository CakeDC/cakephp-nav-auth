CakeDC Navision Authenticate plugin for CakePHP
===================

[![Build Status](https://secure.travis-ci.org/CakeDC/cakephp-nav-auth.png?branch=master)](http://travis-ci.org/CakeDC/cakephp-nav-auth)
[![Coverage Status](https://img.shields.io/codecov/c/gh/CakeDC/cakephp-nav-auth.svg?style=flat-square)](https://codecov.io/gh/CakeDC/cakephp-nav-auth)
[![Downloads](https://poser.pugx.org/CakeDC/cakephp-nav-auth/d/total.png)](https://packagist.org/packages/CakeDC/cakephp-nav-auth)
[![Latest Version](https://poser.pugx.org/CakeDC/cakephp-nav-auth/v/stable.png)](https://packagist.org/packages/CakeDC/cakephp-nav-auth)
[![License](https://poser.pugx.org/CakeDC/cakephp-nav-auth/license.svg)](https://packagist.org/packages/CakeDC/cakephp-nav-auth)

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require cakedc/cakephp-nav-auth
```

## Configuration

You need to configure the following settings using `Configure`:

```
Configure::write('NavAuth', [
    'url' => [
        'soap' => [
            // Protocol (http, https)
            'protocol' => 'https',
            // Server
            'server' => '',
            // Port
            'port' => '',
            // Instance
            'instance' => '',
            // Company String
            'company' => '',
            //Type
            'type' => '',
            //Endpoint
            'endpoint' => '',
        ],
        'odata' => [
            //Protocol (http, https)
            'protocol' => 'https',
            //Server
            'server' => '',
            //Port
            'port' => '',
            //Instance
            'instance' => '',
            //Method
            'method' => '',
            //Type (usually empty for odata)
            'type' => '',
            //Company String
            'company' => '',
            //Endpoint
            'endpoint' => '',
        ]

    ],
    'auth' => [
        // NTML authentication params
        'ntlm' => [
            'domain' => '',
            'username' => '',
            'password' => ''
        ],

    ],
]);
```

## Usage

The plugin includes two authenticate objects: `Soap` and `OData`. To use any of them (or both) you can include the following code in your `AppController::initialize()`.
```
$this->loadComponent('Auth', [
    'authenticate' => [
        'CakeDC/NavAuth.Soap'
    ]
]);
```

```
$this->loadComponent('Auth', [
    'authenticate' => [
        'CakeDC/NavAuth.OData'
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
