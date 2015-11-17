<?php
namespace Behomaler\Hoimi;

use Behomaler\Loula\LoulaAdapter;
use Hoimi\BaseAction;
use Hoimi\Response\Json;

class HoimiApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Behomaler\Hoimi\HoimiApplication
     */
    private $target = null;
    private $router = null;
    private $config = null;
    private $dummyHeaders = null;
    private $dummyParameters = null;
    private $dummyFiles = null;
    private $action = null;
    private $response = null;
    public function setUp()
    {
        $this->dummyHeaders = array('REQUEST_URI' => '/dummy', 'REQUEST_METHOD' => 'POST');
        $this->dummyParameters = array('querySring' => 'value');
        $this->dummyFiles = array('files' => array('name' => 'xyzzy.jpg', 'tmp_name'  => '/tmp/hoge'));
        $this->action = \Phake::mock('\Behomaler\Hoimi\HoimiAction');
        $this->router = \Phake::mock('\Hoimi\Router');
        $this->config = \Phake::mock('\Hoimi\Config');
        $this->response = new Json(array('hoge' => 'fuga'), array('OAuthRealm: hogefugapiyo'));
        $this->target = \Phake::partialMock('\Behomaler\Hoimi\HoimiApplication', $this->router, $this->config);
    }

    public function testRequest ()
    {
        $request = $this->target->createRequest($this->dummyHeaders, $this->dummyParameters, $this->dummyFiles);
        \Phake::when($this->router)->run($request)->thenReturn(array($this->action,'post'));
        \Phake::when($this->action)->post()->thenReturn($this->response);
        $result = $this->target->request($request);
        $this->assertInstanceOf('\Behomaler\Response', $result);
        $this->assertSame('{"hoge":"fuga"}', $result->getBody());
        $this->assertEquals(array('OAuthRealm: hogefugapiyo', 'Content-type: application/json'), $result->getHeaders());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequestInvalid ()
    {
        $result = $this->target->request(new LoulaAdapter('GET', '/dummy'));
    }


    public function testCreateRequest ()
    {
        $result = $this->target->createRequest($this->dummyHeaders, $this->dummyParameters, $this->dummyFiles);
        $this->assertInstanceOf('\Behomaler\Hoimi\HoimiAdapter', $result);
        $this->assertSame($this->dummyHeaders, $result->getHeaders());
        $this->assertSame($this->dummyParameters, $result->getBody());
        $this->assertSame($this->dummyFiles, $result->getAttachmentFiles());
    }

    public function testInstanceOf ()
    {
        $this->assertInstanceOf('\Behomaler\Hoimi\HoimiApplication', $this->target);
    }
}
class HoimiAction extends BaseAction
{
    public function post()
    {
        return null;
    }
}