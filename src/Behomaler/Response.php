<?php
namespace Behomaler;
class Response
{
    private $headers = array();
    private $body = array();
    public function __construct($headers, $body)
    {
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}