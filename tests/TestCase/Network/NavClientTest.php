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

use CakeDC\NavAuth\Network\NavClient;
use CakeDC\NavAuth\Network\NTLMSoapClient;
use Cake\TestSuite\TestCase;

/**
 * CakeDC\NavAuth\Network\NavClient Test Case
 */
class NavClientTest extends TestCase
{

    /**
     * @var NTLMSoapClient
     */
    public $NTLMClient;

    /**
     * @var NavClient
     */
    public $NavClient;

    /**
     * @var \stdClass Login result
     */
    public $loginResult;

    /**
     * Test Setup
     */
    public function setUp()
    {
        parent::setUp();
        $this->NTLMClient = $this->getMockBuilder('CakeDC\NavAuth\Network\NTLMSoapClient')
            ->setMethods(['Login'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->NavClient = $this->getMockBuilder('CakeDC\NavAuth\Network\NavClient')
            ->setMethods(['_getNTLMSoapClient'])
            ->getMock();
        $this->loginResult = new \stdClass();
    }

    /**
     * Test get user with successful result
     */
    public function testGetUserSuccess()
    {
        $this->loginResult->return_value = '{"Status": "1", "user":"test"}';
        $credentials = [
            'id' => 'test',
            'pw' => 'password'
        ];
        $this->NTLMClient->expects($this->once())
            ->method('Login')
            ->with($credentials)
            ->will($this->returnValue($this->loginResult));
        $this->NavClient->expects($this->once())
            ->method('_getNTLMSoapClient')
            ->will($this->returnValue($this->NTLMClient));
        $result = $this->NavClient->getUser($credentials['id'], $credentials['pw']);
        $this->assertEquals(['Status' => '1', 'user' => 'test'], $result);
    }

    /**
     * Test getUser with false result
     */
    public function testGetUserFailure()
    {
        $this->loginResult->return_value = '{"Status": "0"}';
        $credentials = [
            'id' => 'test',
            'pw' => 'password'
        ];
        $this->NTLMClient->expects($this->once())
            ->method('Login')
            ->with($credentials)
            ->will($this->returnValue($this->loginResult));
        $this->NavClient->expects($this->once())
            ->method('_getNTLMSoapClient')
            ->will($this->returnValue($this->NTLMClient));
        $result = $this->NavClient->getUser($credentials['id'], $credentials['pw']);
        $this->assertFalse($result);
    }
}
