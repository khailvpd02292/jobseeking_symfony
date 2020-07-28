<?php

namespace App\Listeners;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RequestListener implements EventSubscriberInterface{

        public function onKernelRequest(GetResponseEvent $event){
            $request = $event->getRequest();

            $request->attributes->set('refresh_token',$request->cookies->get('key:REFRESH_TOKEN'));

        }

        public static function getSubscribedEvents(){
            return [
                KernelEvents::REQUEST =>[
                    ['onKernelRequest']
                ]
            ];
        }

}