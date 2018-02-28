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
use CakeDC\Users\Controller\Component\UsersAuthComponent;
use Cake\Auth\FormAuthenticate;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;

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
class NavAuthenticate extends FormAuthenticate
{
    /**
     * @var string Type (SOAP, OData)
     */
    protected $_type;

    /**
     * Find a user record in using the username and password provided.
     *
     * @param string $username The username/identifier.
     * @param string|null $password The password, if not provided password checking is skipped
     *   and result of find is returned.
     * @return bool|array Either false on failure, or an array of user data.
     */
    protected function _findUser($username, $password = null)
    {
        if (empty($this->_type)) {
            throw new InternalErrorException(__('A subclass of NavAuthenticate must be used and it should define property $_type with a valid type: "soap", "odata"'));
        }

        $result = $this->_getNavClient()->getUser($username, $password, $this->_type);

        if (!empty($result) && Plugin::loaded('CakeDC/Users')) {
            $user = $this->_map($username, $password, $result);
            if (!empty($result)) {
                if ($user->get('social_accounts')) {
                    $this->_registry->getController()->dispatchEvent(UsersAuthComponent::EVENT_AFTER_REGISTER, compact('user'));
                }
                $this->setConfig('contain', ['SocialAccounts']);
                $result = parent::_findUser($user->username);
            }
        }

        return $result;
    }

    /**
     * Return a new instance of NavClient
     * @return NavClient
     */
    protected function _getNavClient()
    {
        return new NavClient();
    }

    /**
     * Map external user to users plugin structure
     *
     * @param string $username Username
     * @param string $password Password
     * @param array $data Data
     * @return mixed
     */
    protected function _map($username, $password, $data)
    {
        $options = [
            'use_email' => false,
            'validate_email' => false,
            'token_expiration' => false,
        ];
        $data = array_merge([
            'username' => $username,
            'password' => $password,
            'active' => true,
            'link' => '#',
            'is_superuser' => false,
            'credentials' => [
                'token' => Security::hash($password)
            ],
        ], $data);

        $userModel = Configure::read('Users.table');
        $User = TableRegistry::get($userModel);
        $user = $User->socialLogin($data, $options);

        return $user;
    }
}
