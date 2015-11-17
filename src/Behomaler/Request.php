<?php
namespace Behomaler;

interface Request
{
    public function getHeaders();
    public function getBody();
    public function getAttachmentFiles();
}