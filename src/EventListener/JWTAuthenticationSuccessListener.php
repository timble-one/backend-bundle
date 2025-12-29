<?php

namespace TimbleOne\BackendBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use ApiPlatform\Metadata\IriConverterInterface;

#[AsEventListener(event: Events::AUTHENTICATION_SUCCESS, method: 'onAuthenticationSuccess')]
class JWTAuthenticationSuccessListener
{
    public function __construct(private readonly IriConverterInterface $iriConverter)
    {
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (get_class($user) !== 'App\Entity\User') {
            return;
        }

        $data = $event->getData();
        $data['id'] = $this->iriConverter->getIriFromResource($user);

        $event->setData($data);
    }
}
