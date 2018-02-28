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

use CakeDC\NavAuth\Auth\SoapAuthenticate;
use CakeDC\NavAuth\Network\NavClient;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * CakeDC\NavAuth\Auth\SoapAuthenticate Test
 */
class SoapAuthenticateTest extends TestCase
{
    /**
     * @var SoapAuthenticate
     */
    public $SoapAuthenticate;

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
        $this->SoapAuthenticate = $this->getMockBuilder('CakeDC\NavAuth\Auth\SoapAuthenticate')
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
        $this->SoapAuthenticate->expects($this->once())
            ->method('_getNavClient')
            ->will($this->returnValue($this->NavClient));
        $request = (new ServerRequest())->withData('username', 'username')->withData('password', 'password');
        $this->assertEquals(['Status' => '1', 'user' => 'test'], $this->SoapAuthenticate->authenticate($request, new Response()));
    }

    /**
     * Test authenticate
     */
    public function testAuthenticateFailure()
    {
        $this->NavClient->expects($this->once())->method('getUser')
            ->with('username', 'password')
            ->will($this->returnValue(false));
        $this->SoapAuthenticate->expects($this->once())
            ->method('_getNavClient')
            ->will($this->returnValue($this->NavClient));
        $request = (new ServerRequest())->withData('username', 'username')->withData('password', 'password');
        $this->assertFalse($this->SoapAuthenticate->authenticate($request, new Response()));
    }
}
