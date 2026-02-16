<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ( $exception instanceof NotFoundHttpException )
        {
            // get the message from the exception
            $message = $exception->getMessage();
//            $message = 'Die angeforderte Ressource wurde nicht gefunden.';
            // create a custom response
            $response = new Response();
            $response->setContent(json_encode(['error'=>$message]));
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->headers->set('Content-Type', 'application/json');

            // set the response to the event
            $event->setResponse($response);
        }
    }

}