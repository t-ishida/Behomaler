<?php
namespace Behomaler;

interface Application
{
    public function getDomainName();
    public function getProtocolName();
    public function createRequest($headers, $body, $files = null);
    public function request(Request $request);
}