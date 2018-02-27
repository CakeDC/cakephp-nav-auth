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
use Cake\Error\Debugger;
use Cake\Log\Log;
use Cake\Network\Exception\InternalErrorException;
use Cake\Utility\Hash;

/**
 * Class NavClient
 * @package CakeDC\NavAuth\Network
 */
class NavClient
{
    /**
     * Type SOAP
     */
    const TYPE_SOAP = 'soap';
    /**
     * Type OData
     */
    const TYPE_ODATA = 'odata';

    /**
     * Get user from webservice
     * @param string $username Username
     * @param string $password Password
     * @param string $type (soap, odata)
     * @return bool|mixed
     */
    public function getUser($username, $password, $type = self::TYPE_SOAP)
    {
        try {
            $credentials = [
                'id' => $username,
                'pw' => $password,
            ];
            $baseURL = sprintf(
                '%s://%s:%s/%s/%s/%s/%s/%s',
                Configure::read("NavAuth.url.$type.protocol"),
                Configure::read("NavAuth.url.$type.server"),
                Configure::read("NavAuth.url.$type.port"),
                Configure::read("NavAuth.url.$type.instance"),
                Configure::read("NavAuth.url.$type.method"),
                Configure::read("NavAuth.url.$type.company"),
                Configure::read("NavAuth.url.$type.type"),
                Configure::read("NavAuth.url.$type.endpoint")
            );

            Log::info(__('Connecting to webservice: {0}', $baseURL));
            $type = '_' . $type;
            $result = $this->{$type}($baseURL, $credentials);
            Log::debug(__('Connection successful - Response: {0}', $result));

            return $result;
        } catch (\Exception $e) {
            Log::error(__('An error has occurred connecting to webservice: {0}', $e->getMessage()));
        }

        return false;
    }

    /**
     * Connect to OData server for credentials
     *
     * @param string $baseURL Base url
     * @param array $credentials Credentials to login
     *
     * @return array
     */
    protected function _odata($baseURL, $credentials)
    {
        $client = $this->_getNTLMODataClient();
        $baseURL .= '?$filter=' . urlencode(sprintf(
            "%s eq '%s' and %s eq '%s'",
            Configure::read('NavAuth.url.odata.loginField'),
            Hash::get($credentials, 'id'),
            Configure::read('NavAuth.url.odata.passwordField'),
            Hash::get($credentials, 'pw')
        ));

        $response = $client->doRequest($baseURL);

        $result = json_decode($response, true);

        if (empty($result['value'][0])) {
            return false;
        }

        return Hash::get($result, 'value.0');
    }

    /**
     * Soap connection
     * @param string $baseURL Base Url
     * @param array $credentials Credentials to login
     *
     * @return mixed
     */
    protected function _soap($baseURL, $credentials)
    {
        stream_wrapper_unregister('https');
        if (!stream_wrapper_register('https', NTLMStream::class)) {
            throw new InternalErrorException(__('Failed to register protocol for NTLMStream'));
        }

        $client = $this->_getNTLMSoapClient($baseURL, [
            'trace' => Configure::read('debug'),
            'cache_wsdl' => false,
            'exceptions' => true
        ]);

        $response = $client->Login($credentials);
        $result = json_decode($response->return_value, true);
        if (empty($result['Status'])) {
            return false;
        }

        return $result;
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

    /**
     * @return NTLMODataClient
     */
    protected function _getNTLMODataClient()
    {
        return new NTLMODataClient();
    }
}
