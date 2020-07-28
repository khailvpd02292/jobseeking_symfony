<?php

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RefreshTokenListener implements EventSubscriberInterface{

        private $secure = false;
        private $ttl;

        public function __constructor($ttl){

        $this->ttl = $ttl;
        }

        public function setRefreshToken(AuthenticationSuccessEvent $event){
            $refreshToken = $event->getData()['refresh_token'];
            $response = $event->getResponse();
            if($refreshToken){
            $response->headers->setCookie(
                    new Cookie('name: REFRESH_TOKEN', $refreshToken, (new \DateTime())
                    ->add(new \DateInterval('PT' . 3600 . 'S'))
                    ),'/',null,$this->secure);  
            }
        }

        public static function getSubscribedEvents(){
            return ['lexik_jwt_authentication.on_authentication_success'
                =>['setRefreshToken']
            ];
        }

}