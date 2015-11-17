<?php
namespace Behomaler;


use Behomaler\Hoimi\HoimiAdapter;
use Behomaler\Loula\LoulaAdapter;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Behomaler\Client
     */
    private $target = null;
    private $hoimiRequest = null;
    private $loulaRequest = null;
    private $application1 = null;
    private $application2 = null;
    private $dummyHeaders = null;
    private $dummyParameters = null;
    private $response = null;
    public function setUp()
    {
        $this->hoimiRequest = new HoimiAdapter(\Hoimi\Router::getInstance(), new \Hoimi\Config(), array(), array());
        $this->loulaRequest = new LoulaAdapter('GET' ,'/dummy');
        $this->response = new Response($this->dummyHeaders, $this->dummyParameters);
        $this->dummyHeaders = array('REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/dummy');
        $this->dummyParameters = array('queryString' => 'value');

        $this->application1 = \Phake::mock('\Behomaler\Application');
        $this->application2 = \Phake::mock('\Behomaler\Application');

        $this->target = \Behomaler\Client::getInstance();
        $this->target->clear();
        $this->target->add($this->application1)->add($this->application2);
        \Phake::when($this->application1)->getDomainName()->thenReturn('loula');
        \Phake::when($this->application2)->getDomainName()->thenReturn('hoimi');
    }

    public function testRequest ()
    {
        \Phake::when($this->application2)->createRequest($this->dummyHeaders, $this->dummyParameters, null)->thenReturn($this->hoimiRequest);
        \Phake::when($this->application2)->request($this->hoimiRequest)->thenReturn($this->response);
        $result = $this->target->request('hoimi', 'GET', '/dummy', $this->dummyParameters);
        $this->assertSame($this->response, $result);
        \Phake::verify($this->application1, \Phake::never())->createRequest(\Phake::anyParameters());
        \Phake::verify($this->application1, \Phake::never())->send(\Phake::anyParameters());
    }

    public function testSend ()
    {
        \Phake::when($this->application2)->request($this->hoimiRequest)->thenReturn($this->response);
        $result = $this->target->send('hoimi', $this->hoimiRequest);
        $this->assertSame($this->response, $result);
        \Phake::verify($this->application1, \Phake::never())->send(\Phake::anyParameters());
    }

    public function testCreate ()
    {
        \Phake::when($this->application1)->createRequest($this->dummyHeaders, $this->dummyParameters, null)->thenReturn($this->loulaRequest);
        $result = $this->target->create('loula', $this->dummyHeaders, $this->dummyParameters);
        $this->assertSame($this->loulaRequest, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidApplication()
    {
        $this->target->create('hoge', array(), array());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequestInvalidApplication()
    {
        $this->target->request('hoge', 'fuga', 'piyo');
    }
}