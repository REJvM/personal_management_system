<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ApiKeySubscriber implements EventSubscriberInterface
{
    private $apiKey;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->apiKey = $parameterBag->get('api_key');
    }

    public function onKernelController(ControllerEvent $event)
    {
        $request = $event->getRequest();

        /**  
         * block all API calls if there is no api key in the headers.
         * Exclude swagger interface from this key requirement. 
         */
        if (
            strpos($request->getPathInfo(), '/api/v1/') === 0 &&
            in_array($request->getPathInfo(), ['/api/v1/', '/api/v1/openapi.json']) === false
        ) {
            $headers =  array_change_key_case(getallheaders(), CASE_UPPER);
            if (
                array_key_exists('API-KEY', $headers) === false ||
                $headers['API-KEY'] !== $this->apiKey
            ) {
                throw new AccessDeniedHttpException('This action needs a valid key!');
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
