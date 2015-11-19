<?php
namespace Behomaler\Loula;
use Behomaler\Application;
use Behomaler\Request;
use Behomaler\Response;

abstract class LoulaApplication implements Application
{
    /**
     * @var \Loula\HttpClient
     */
    private $client = null;
    public function __construct (\Loula\HttpClient $client)
    {
        $this->client = $client;
    }
    public function getProtocolName()
    {
        return 'https';
    }

    public function createRequest($headers, $body, $files = null)
    {
        return new LoulaAdapter(
            $headers['REQUEST_METHOD'],
            $this->getProtocolName() . '://' . $this->getDomainName() . $headers['REQUEST_URI'],
            $body,
            $files,
            $headers
        );
    }

    public function request(Request $request)
    {
        if (!($request instanceof LoulaAdapter)) {
            throw new \InvalidArgumentException('invalid request');
        }
        $result = $this->client->sendOne($request);
        return new Response(explode("\n", $result->getHeader()), $result->getBody());
    }

    /**
     * @return \Loula\HttpClient
     */
    public function getClient()
    {
        return $this->client;
    }

}