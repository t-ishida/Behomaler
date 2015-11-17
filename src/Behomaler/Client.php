<?php
namespace Behomaler;

class Client
{
    public static function getInstance ()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new static;
        }
        return $instance;
    }

    private $applications = array();

    private function __construct()
    {
    }

    public function clear()
    {
        $this->applications = array();
    }

    public function add(Application $application)
    {
        $this->applications[] = $application;
        return $this;
    }

    public function remove($domainName)
    {
        $newApplications = array();
        foreach($this->applications as $application) {
            if ($application->getDomainName() !== $domainName)  {
                $newApplications[] = $application;
            }
        }
        $this->applications = $newApplications;
        return $this;
    }

    public function request ($domainName, $method, $url, $params = null, $files = null)
    {
        return $this->send($domainName, $this->create($domainName, array(
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $url
        ), $params ?: array()));
    }

    public function send($domainName, Request $request)
    {
        foreach($this->applications as $application) {
            if ($application->getDomainName() === $domainName) {
                return $application->request($request);
            }
        }
        throw new \InvalidArgumentException('bad app name:' . $domainName);
    }

    public function create ($domainName, array $headers, array $body, array $files = null)
    {
        foreach($this->applications as $application) {
            if ($application->getDomainName() === $domainName) {
                return $application->createRequest($headers, $body, $files);
            }
        }
        throw new \InvalidArgumentException('bad app name:' . $domainName);
    }
}