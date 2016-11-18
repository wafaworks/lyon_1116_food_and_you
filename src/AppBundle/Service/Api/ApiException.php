<?php
/**
 * Created by PhpStorm.
 * User: apodgorbunschih
 * Date: 28/1/2016
 * Time: 16:31
 */

namespace AppBundle\Service\Api;

use AppBundle\Exception\Api\ApiExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ApiException
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof ApiExceptionInterface) {
            $response = new JsonResponse(array(
                'errors' => $exception->getMessage(),
            ), $exception->getCode());

            $event->setResponse($response);
        }

        return $event;
    }
}
