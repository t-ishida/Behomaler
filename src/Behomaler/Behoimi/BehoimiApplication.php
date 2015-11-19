<?php
namespace Behomaler\Behoimi;


use Behoimi\OAuth\InvalidTokenException;
use Behomaler\Hoimi\HoimiAdapter;
use Behomaler\Hoimi\HoimiApplication;
use Behomaler\Request;

abstract class BehoimiApplication extends HoimiApplication
{
    protected $accessToken = null;
    protected $refreshToken = null;
    /**
     * @var \Loula\AccessTokenListener[]
     */
    private $listeners = array();

    public function __construct(
        \Hoimi\Router $router,
        \Hoimi\Config $config,
        $accessToken,
        $refreshToken,
        array $listeners = array()) {
        parent::__construct($router, $config);
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->listeners = $listeners;
    }

    public function createRequest($headers, $body, $files = null)
    {
        $body['access_token'] = $this->accessToken;
        return parent::createRequest($headers, $body, $files);
    }

    public function request(Request $request)
    {
        if (!($request instanceof HoimiAdapter)) {
            throw new \InvalidArgumentException('bad request');
        }
        $result = null;
        try {
            $result = parent::request($request);
        } catch (InvalidTokenException $e) {
            $this->refreshAccessToken();
            $result = $this->request($this->createRequest(
                $request->getHeaders(),
                $request->getBody(),
                $request->getAttachmentFiles()
             ));
        }
        return $result;
    }

    public function refreshAccessToken ()
    {
        $this->refreshToken();
        foreach($this->listeners as $listener) {
            $listener->changedAccessTokenAt($this->accessToken, $this->refreshToken);
        }
    }

    public function exchangeCode ()
    {
        $this->exchangeCode();
        foreach($this->listeners as $listener) {
            $listener->changedAccessTokenAt($this->accessToken, $this->refreshToken);
        }
    }

    abstract public function refreshToken();
    abstract public function exchangeAccessToken();
}