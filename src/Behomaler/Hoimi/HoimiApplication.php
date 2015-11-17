<?php
namespace Behomaler\Hoimi;

use Behomaler\Application;
use Behomaler\Request;
use Behomaler\Response;

abstract class HoimiApplication implements Application
{
    private $router = null;
    private $config = null;

    public function __construct(\Hoimi\Router $router, \Hoimi\Config $config)
    {
        $this->router = $router;
        $this->config = $config;
    }

    public function getProtocolName()
    {
        return 'hoimi';
    }

    public function createRequest($headers, $body, $files = null)
    {
        return new HoimiAdapter($this->getRouter(), $this->getConfig(), $headers, $body, $files);
    }

    public function request(Request $request)
    {
        if (!($request instanceof HoimiAdapter)) {
            throw new \InvalidArgumentException('invalid request');
        }

        list($action, $method) = $request->getRouter()->run($request);
        $action->setConfig($request->getConfig());
        $action->setRequest($request);
        $response = $action->$method();
        return new Response($response->getHeaders(), $response->getContent());
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getRouter()
    {
        return $this->router;
    }
}