<?php

declare(strict_types=1);

namespace ScooterVolt\UserService\Shared\Domain\Event;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use ScooterVolt\UserService\User\Domain\UserEmail;
use ScooterVolt\UserService\User\Domain\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;


class JWTCreatedListener
{
    /**
     * @param RequestStack $requestStack
     */
    public function __construct(private RequestStack $requestStack, private UserRepository $userRepository)
    {
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $payload = $event->getData();
        $payload['ip'] = $request->getClientIp();

        $user = $this->userRepository->findByEmail(new UserEmail($payload['username']));

        if ($user) {
            $payload['id'] = $user->getId();
            $payload['name'] = $user->getFullname()->name();
            $payload['surname'] = $user->getFullname()->surname();
        }

        $event->setData($payload);

        $header = $event->getHeader();
        $header['cty'] = 'JWT';

        $event->setHeader($header);
    }
}