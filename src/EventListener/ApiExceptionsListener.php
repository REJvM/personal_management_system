<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApiExceptionListener implements EventSubscriberInterface
{
    /** overwrite return errors for /api path, always return json errors. */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if($exception instanceof HttpException) {
            if($request->getPathInfo() === '/api' || strpos($request->getPathInfo(), '/api/') === 0) {
                $response = new JsonResponse([
                    'message' => $exception->getMessage(),
                    'statusCode' => $exception->getStatusCode(),
                ], $exception->getStatusCode());
                $event->setResponse($response);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10]
        ];
    }
}