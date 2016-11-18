<?php

namespace AppBundle\Exception\Api;

class ApiUnauthorizedException extends \Exception implements ApiExceptionInterface
{
    protected $code = 401;
    protected $message = "Unauthorized";
}
