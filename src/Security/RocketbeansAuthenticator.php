<?php

namespace App\Security;

use App\Entity\User;
use App\OauthProviders\RockebeansResourceOwner;
use App\Service\SyncService;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class RocketbeansAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;
    private $syncService;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router, SyncService $syncService)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->syncService = $syncService;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'connect_rocketbeans_redirect';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getRocketbeansClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var RockebeansResourceOwner $rocketbeansUser */
        $rocketbeansUser = $this->getRocketbeansClient()->fetchUserFromToken($credentials);

        /** @var User $existingUser */
        $existingUser = $this->em->getRepository(User::class)
            ->findOneBy(['rocketbeansId' => $rocketbeansUser->getId()]);

        // Update existing users access token
        if ($existingUser) {
            $existingUser->setUsername($rocketbeansUser->getDisplayName());
            $existingUser->setRbtvAccessToken($credentials->getToken());
            $existingUser->setRbtvRefreshToken($credentials->getRefreshToken());
            $existingUser->setRbtvExpires($credentials->getExpires());

            $this->em->persist($existingUser);
            $this->em->flush();

            return $existingUser;
        }


        // Create new user
        /** @var User $user */
        $user = new User();

        $user->setUsername($rocketbeansUser->getDisplayName());
        $user->setRocketbeansId($rocketbeansUser->getId());
        $user->setRbtvAccessToken($credentials->getToken());
        $user->setRbtvRefreshToken($credentials->getRefreshToken());
        $user->setRbtvExpires($credentials->getExpires());

        $this->em->persist($user);
        $this->em->flush();


        // Sync subscribed shows of new user
        $this->syncService->syncSubscriptionsOfUser($user);

        return $user;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetUrl = $this->router->generate('app_homepage');

        return new RedirectResponse($targetUrl);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    private function getRocketbeansClient() {
        return $this->clientRegistry->getClient('rocketbeans');
    }
}
