<?php

namespace AppBundle\Exception\Api;

class ApiForbiddenException extends \Exception implements ApiExceptionInterface
{
    protected $code = 403;
    protected $message = "Forbidden";
}
