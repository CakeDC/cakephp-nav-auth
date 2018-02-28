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

namespace CakeDC\NavAuth\Test\TestCase\Auth;

use CakeDC\NavAuth\Auth\ODataAuthenticate;
use CakeDC\NavAuth\Network\NavClient;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * CakeDC\NavAuth\Auth\ODataAuthenticate Test
 */
class ODataAuthenticateTest extends TestCase
{
    /**
     * Fixtures
     * @var array
     */
    public $fixtures = ['plugin.CakeDC/Users.users', 'plugin.CakeDC/Users.social_accounts'];

    /**
     * @var ODataAuthenticate
     */
    public $ODataAuthenticate;

    /**
     * @var NavClient
     */
    public $NavClient;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->ODataAuthenticate = $this->getMockBuilder('CakeDC\NavAuth\Auth\ODataAuthenticate')
            ->setMethods(['_getNavClient'])
            ->setConstructorArgs([new ComponentRegistry()])
            ->getMock();
        $this->NavClient = $this->getMockBuilder('CakeDC\NavAuth\Network\NavClient')
            ->setMethods(['getUser'])
            ->getMock();
    }

    /**
     * Test authenticate
     */
    public function testAuthenticateSuccessful()
    {
        $this->NavClient->expects($this->once())->method('getUser')
            ->with('username', 'password')
            ->will($this->returnValue(['Status' => '1', 'user' => 'test']));
        $this->ODataAuthenticate->expects($this->once())
            ->method('_getNavClient')
            ->will($this->returnValue($this->NavClient));
        $request = (new ServerRequest())->withData('username', 'username')->withData('password', 'password');
        $this->assertEquals(['Status' => '1', 'user' => 'test'], $this->ODataAuthenticate->authenticate($request, new Response()));
    }

    /**
     * Test authenticate
     */
    public function testAuthenticateFailure()
    {
        $this->NavClient->expects($this->once())->method('getUser')
            ->with('username', 'password')
            ->will($this->returnValue(false));
        $this->ODataAuthenticate->expects($this->once())
            ->method('_getNavClient')
            ->will($this->returnValue($this->NavClient));
        $request = (new ServerRequest())->withData('username', 'username')->withData('password', 'password');
        $this->assertFalse($this->ODataAuthenticate->authenticate($request, new Response()));
    }
}
