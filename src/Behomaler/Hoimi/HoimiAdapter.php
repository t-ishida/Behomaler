<?php
namespace Behomaler\Hoimi;
use Behomaler\Request;

class HoimiAdapter extends \Hoimi\Request implements Request
{
    private $router = null;
    private $config = null;
    private $_files = null;
    public function __construct($router, $config, $headers, $request, $files = null)
    {
        parent::__construct($headers, $request, $files);
        $this->router = $router;
        $this->config = $config;
        $this->_files  = $files;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getAttachmentFiles()
    {
        return $this->_files;
    }
}