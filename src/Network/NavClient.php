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
use Cake\Log\Log;
use Cake\Network\Exception\InternalErrorException;

/**
 * Class NavClient
 * @package CakeDC\NavAuth\Network
 */
class NavClient
{

    /**
     * Get user from webservice
     * @param string $username Username
     * @param string $password Password
     * @return bool|mixed
     */
    public function getUser($username, $password)
    {
        try {
            $result = $this->_connect($username, $password);
            if (empty($result['Status'])) {
                return false;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error(__('An error has occurred connecting to webservice: {0}', $e->getMessage()));
        }

        return false;
    }

    /**
     * Connect to webservice using credentials
     *
     * @param string $username Username
     * @param string $password Password
     * @return mixed
     */
    protected function _connect($username, $password)
    {
        $credentials = [
            'id' => $username,
            'pw' => $password,
        ];
        $baseURL = sprintf(
            '%s://%s:%s/%s/%s/%s/%s/%s',
            Configure::read('NavAuth.url.protocol'),
            Configure::read('NavAuth.url.server'),
            Configure::read('NavAuth.url.port'),
            Configure::read('NavAuth.url.instance'),
            Configure::read('NavAuth.url.method'),
            Configure::read('NavAuth.url.company'),
            Configure::read('NavAuth.url.type'),
            Configure::read('NavAuth.url.endpoint')
        );
        Log::info(__('Connecting to webservice: {0}', $baseURL));
        stream_wrapper_unregister('https');
        if (!stream_wrapper_register('https', NTLMStream::class)) {
            throw new InternalErrorException(__('Failed to register protocol for NTLMStream'));
        }

        $client = $this->_getNTLMSoapClient($baseURL, [
            'trace' => true,
            'cache_wsdl' => false,
            'exceptions' => true,
            'user' => Configure::read('NavAuth.auth.ntlm.domain') . "\\" . Configure::read('NavAuth.auth.ntlm.username'),
            'password' => Configure::read('NavAuth.auth.ntlm.password')
        ]);
        $result = $client->Login($credentials);
        Log::debug(__('Connection successful - Response: {0}', $result->return_value));

        return json_decode($result->return_value, true);
    }

    /**
     * @param string $url Url
     * @param array $options Options for client
     * @return NTLMSoapClient
     */
    protected function _getNTLMSoapClient($url, $options)
    {
        return new NTLMSoapClient($url, $options);
    }
}
