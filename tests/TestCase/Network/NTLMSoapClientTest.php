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
class NTLMSoapClientTest extends TestCase
{
    /**
     * Fixtures
     * @var array
     */
    public $fixtures = ['plugin.CakeDC/Users.users', 'plugin.CakeDC/Users.social_accounts'];

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
            ->setMethods(['_executeCurl'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->loginResult = new \stdClass();
    }

    /**
     * Test doRequest method
     */
    public function testDoRequest()
    {
        $this->loginResult->return_value = '{"Status": "1", "user":"test"}';
        $location = 'location';
        $request = 'request';
        $headers = [
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "Login"',
        ];
        $this->NTLMClient->expects($this->once())
            ->method('_executeCurl')
            ->with($location, $request, $headers)
            ->will($this->returnValue($this->loginResult));

        $result = $this->NTLMClient->__doRequest($request, $location, 'Login', 1);
        $this->assertEquals($this->loginResult, $result);
        $lastHeaders = $this->NTLMClient->__getLastRequestHeaders();
        $this->assertEquals(implode("\n", $headers) . "\n", $lastHeaders);
    }
}
