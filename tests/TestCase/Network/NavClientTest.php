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
use CakeDC\NavAuth\Network\NTLMODataClient;
use CakeDC\NavAuth\Network\NTLMSoapClient;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * CakeDC\NavAuth\Network\NavClient Test Case
 */
class NavClientTest extends TestCase
{

    /**
     * @var NTLMSoapClient
     */
    public $NTLMSoapClient;

    /**
     * @var NTLMODataClient
     */
    public $NTLMODataClient;

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
        $this->NTLMSoapClient = $this->getMockBuilder('CakeDC\NavAuth\Network\NTLMSoapClient')
            ->setMethods(['Login'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->NTLMODataClient = $this->getMockBuilder('CakeDC\NavAuth\Network\NTLMODataClient')
            ->setMethods(['doRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->NavClient = $this->getMockBuilder('CakeDC\NavAuth\Network\NavClient')
            ->setMethods(['_getNTLMSoapClient', '_getNTLMODataClient'])
            ->getMock();
        $this->loginResult = new \stdClass();
        Configure::write("NavAuth.url.odata.protocol", 'https');
        Configure::write("NavAuth.url.odata.server", 'server');
        Configure::write("NavAuth.url.odata.port", 'port');
        Configure::write("NavAuth.url.odata.instance", 'instance');
        Configure::write("NavAuth.url.odata.method", 'method');
        Configure::write("NavAuth.url.odata.company", 'company');
        Configure::write("NavAuth.url.odata.endpoint", 'endpoint');
    }

    /**
     * Test get user with successful result
     */
    public function testSoapGetUserSuccess()
    {
        $this->loginResult->return_value = '{"Status": "1", "user":"test"}';
        $credentials = [
            'id' => 'test',
            'pw' => 'password'
        ];
        $this->NTLMSoapClient->expects($this->once())
            ->method('Login')
            ->with($credentials)
            ->will($this->returnValue($this->loginResult));
        $this->NavClient->expects($this->once())
            ->method('_getNTLMSoapClient')
            ->will($this->returnValue($this->NTLMSoapClient));
        $result = $this->NavClient->getUser($credentials['id'], $credentials['pw']);
        $this->assertEquals(['Status' => '1', 'user' => 'test'], $result);
    }

    /**
     * Test getUser with false result
     */
    public function testSoapGetUserFailure()
    {
        $this->loginResult->return_value = '{"Status": "0"}';
        $credentials = [
            'id' => 'test',
            'pw' => 'password'
        ];
        $this->NTLMSoapClient->expects($this->once())
            ->method('Login')
            ->with($credentials)
            ->will($this->returnValue($this->loginResult));
        $this->NavClient->expects($this->once())
            ->method('_getNTLMSoapClient')
            ->will($this->returnValue($this->NTLMSoapClient));
        $result = $this->NavClient->getUser($credentials['id'], $credentials['pw']);
        $this->assertFalse($result);
    }

    /**
     * Test get user with successful result
     */
    public function testODataGetUserSuccess()
    {
        $this->loginResult = '{"@odata.context":"test-context","value":[{"user":"test"}]}';
        $credentials = [
            'id' => 'test',
            'pw' => 'password'
        ];
        $this->NTLMODataClient->expects($this->once())
            ->method('doRequest')
            ->with(
                'https://server:port/instance/method/company//endpoint?$filter=Go2000_id+eq+%27test%27+and+Go2000_pw+eq+%27password%27'
            )
            ->will($this->returnValue($this->loginResult));
        $this->NavClient->expects($this->once())
            ->method('_getNTLMODataClient')
            ->will($this->returnValue($this->NTLMODataClient));
        $result = $this->NavClient->getUser($credentials['id'], $credentials['pw'], NavClient::TYPE_ODATA);
        $this->assertEquals(['user' => 'test'], $result);
    }

    /**
     * Test getUser with false result
     */
    public function testODataGetUserFailure()
    {
        $this->loginResult = '{"@odata.context":"test-context","value":[]}';
        $credentials = [
            'id' => 'test',
            'pw' => 'password'
        ];
        $this->NTLMODataClient->expects($this->once())
            ->method('doRequest')
            ->with(
                'https://server:port/instance/method/company//endpoint?$filter=Go2000_id+eq+%27test%27+and+Go2000_pw+eq+%27password%27'
            )
            ->will($this->returnValue($this->loginResult));
        $this->NavClient->expects($this->once())
            ->method('_getNTLMODataClient')
            ->will($this->returnValue($this->NTLMODataClient));
        $result = $this->NavClient->getUser($credentials['id'], $credentials['pw'], NavClient::TYPE_ODATA);
        $this->assertFalse($result);
    }
}
