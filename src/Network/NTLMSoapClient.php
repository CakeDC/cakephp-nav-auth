<?php
/**
 * Copyright (c) 2008 Invest-In-France Agency http://www.invest-in-france.org
 *
 * Author : Thomas Rabaix
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

namespace CakeDC\NavAuth\Network;

use Cake\Core\Configure;
use SoapClient;

/**
 * Class NTLMSoapClient
 *
 * @package CakeDC\NavAuth\Network
 */
class NTLMSoapClient extends SoapClient
{

    /**
     * Do request against server
     * @param array $request Request
     * @param string $location Location url
     * @param string $action Action
     * @param string $version Version
     * @param int $one_way One way
     *
     * @return mixed
     */
    //phpcs:ignore
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $headers = [
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "' . $action . '"',
        ];
        $this->__last_request_headers = $headers;
        $response = $this->_executeCurl($location, $request, $headers);

        return $response;
    }

    /**
     * Execure curl request
     * @param string $location Location url
     * @param array $request Request
     * @param array $headers Headers
     *
     * @return mixed
     */
    protected function _executeCurl($location, $request, $headers = [])
    {
        //TODO Refactor this using Http Client after implementing NTLM authenticate in CakePHP core
        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
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

    /**
     * @return string
     */
    //phpcs:ignore
    public function __getLastRequestHeaders()
    {
        return implode("\n", $this->__last_request_headers) . "\n";
    }
}
