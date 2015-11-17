<?php
namespace Behomaler\Loula;
use Behomaler\Request;

class LoulaAdapter extends \Loula\HttpRequest implements Request
{

    public function getBody()
    {
        return $this->getParams();
    }

    public function getAttachmentFiles()
    {
        return $this->getFiles();
    }
}