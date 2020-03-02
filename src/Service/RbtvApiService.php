<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class RbtvApiService {

    private $em;
    private $clientRegistry;

    public function __construct(EntityManagerInterface $em, ClientRegistry $clientRegistry)
    {
        $this->em = $em;
        $this->clientRegistry = $clientRegistry;
    }


    public function getClient(User $user)
    {
        $accessToken = $this->getAccessToken($user);

        return new \GuzzleHttp\Client([
            'base_uri' => 'https://api.rocketbeans.tv/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken->getToken(),
            ],
        ]);
    }

    public function getAccessTokenFromDatabase(User $user)
    {
        return new AccessToken([
            'access_token' => $user->getRbtvAccessToken(),
            'refresh_token' => $user->getRbtvRefreshToken(),
            'expires' => $user->getRbtvExpires(),
        ]);
    }

    public function refreshAccessToken(User $user, AccessTokenInterface $existingAccessToken)
    {
        /** @var OAuth2ClientInterface $client */
        $client = $this->clientRegistry->getClient('rocketbeans');
        $provider = $client->getOAuth2Provider();

        $newAccessToken = $provider->getAccessToken('refresh_token', [
            'refresh_token' => $existingAccessToken->getRefreshToken(),
        ]);

        $user->setRbtvAccessToken($newAccessToken->getToken());
        $user->setRbtvExpires($newAccessToken->getExpires());

        $this->em->persist($user);
        $this->em->flush();

        return $newAccessToken;
    }

    public function getAccessToken(User $user)
    {
        $accessToken = $this->getAccessTokenFromDatabase($user);

        if ($accessToken->hasExpired()) {
            $accessToken = $this->refreshAccessToken($user, $accessToken);
        }

        return $accessToken;
    }
}
