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

namespace CakeDC\NavAuth\Auth;

use CakeDC\NavAuth\Network\NavClient;

/**
 * An authentication adapter for AuthComponent. Provides the ability to authenticate using POST
 * data against a Navision SOAP server using NTLM. Can be used by configuring AuthComponent to use it via the AuthComponent::$authenticate config.
 *
 * ```
 *  $this->Auth->authenticate = [
 *      'CakeDC\NavAuth.Soap'
 *  ]
 * ```
 *
 * @see \Cake\Controller\Component\AuthComponent::$authenticate
 */
class SoapAuthenticate extends NavAuthenticate
{
    /**
     * @var string Type
     */
    protected $_type = NavClient::TYPE_SOAP;
}
