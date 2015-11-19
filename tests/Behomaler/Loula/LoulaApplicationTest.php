<?php
/**
 * Date: 15/11/17
 * Time: 15:14.
 */

namespace Behomaler\Loula;


use Behomaler\Hoimi\HoimiAdapter;
use Loula\HttpResponse;

class LoulaApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Behomaler\Loula\LoulaApplication
     */
    private $target = null;
    private $client = null;
    private $response = null;
    private $request = null;
    private $dummyHeaders = null;
    private $dummyParameters = null;
    private $dummyFiles = null;
    public function setUp()
    {
        $this->client = \Phake::mock('\Loula\HttpClient');
        $this->request = \Phake::mock('\Behomaler\Loula\LoulaAdapter');
        $this->response = \Phake::mock('\Behomaler\Loula\LoulaAdapter');
        $this->dummyHeaders = array('REQUEST_URI' => '/dummy', 'REQUEST_METHOD' => 'POST');
        $this->dummyParameters = array('querySring' => 'value');
        $this->dummyFiles = array('files' => array('name' => 'xyzzy.jpg', 'tmp_name'  => '/tmp/hoge'));
        $this->target = \Phake::partialMock('\Behomaler\Loula\LoulaApplication', $this->client);
    }

    public function testRequest ()
    {
        \Phake::when($this->target->getClient())->sendOne($this->request)->thenReturn(
            new HttpResponse(
                "HTTP/1.0 200 OK\r\n\r\n{'hoge':'fuga'}",
                array('http_code' => 200)
            ));
        $result = $this->target->request($this->request);
        $this->assertSame(array('HTTP/1.0 200 OK'), $result->getHeaders());
        $this->assertSame("{'hoge':'fuga'}", $result->getBody());
    }

    public function testCreateRequest ()
    {
        $result = $this->target->createRequest($this->dummyHeaders, $this->dummyParameters, $this->dummyFiles);
        $this->assertInstanceOf('\Behomaler\Loula\LoulaAdapter', $result);
        $this->assertSame($this->dummyHeaders, $result->getHeaders());
        $this->assertSame($this->dummyParameters, $result->getBody());
        $this->assertSame($this->dummyFiles, $result->getAttachmentFiles());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRequestInvalid ()
    {
        $this->target->request(new HoimiAdapter(
            \Hoimi\Router::getInstance(),
            new \Hoimi\Config(),
            $this->dummyHeaders,
            $this->dummyParameters,
            $this->dummyFiles
        ));
    }
}