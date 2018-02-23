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

namespace CakeDC\NavAuth\Network;

use Cake\Core\Configure;

/**
 * Class NTLMODataClient
 *
 * @package CakeDC\NavAuth\Network
 */
class NTLMODataClient
{
    /**
     * Do request against server
     * @param string $location Location url
     *
     * @return mixed
     */
    public function doRequest($location)
    {
        // phpcs:ignore
        //TODO Refactor this using Http Client after implementing NTLM authenticate in CakePHP core
        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt(
            $ch,
            CURLOPT_USERPWD,
            sprintf(
                '%s\\%s:%s',
                Configure::read('NavAuth.auth.ntlm.domain'),
                Configure::read('NavAuth.auth.ntlm.username'),
                Configure::read('NavAuth.auth.ntlm.password')
            )
        );
        $response = curl_exec($ch);

        return $response;
    }
}
