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
        // Protocol (http, https)
        'protocol' => 'https',
        // Server
        'server' => '',
        // Port
        'port' => '',
        // Instance
        'instance' => '\Cake\Auth\DefaultPasswordHasher',
        // Company String
        'company' => '',
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
