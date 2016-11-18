<?php

namespace AppBundle\Exception\Api;

class ApiBadRequestException extends \Exception implements ApiExceptionInterface
{
    protected $code = 400;
    protected $message = "Bad Request";
}
