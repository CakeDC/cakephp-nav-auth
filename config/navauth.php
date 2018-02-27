<?php
/**
 * Copyright 2018, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2018, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$config = [
    'NavAuth' => [
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
                //Login field to filter
                'loginField' => '',
                //Password field to filter
                'passwordField' => ''
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
    ]
];

return $config;
