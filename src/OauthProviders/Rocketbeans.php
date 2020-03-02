<?php

namespace App\OauthProviders;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Rocketbeans extends AbstractProvider {

    use BearerAuthorizationTrait;

    protected $baseUrl = 'https://api.rocketbeans.tv/v1';

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Get provider url to run authorization
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://rocketbeans.tv/oauth2/authorize';
    }

    /**
     * @inheritDoc
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->getBaseUrl() . '/oauth2/token';
    }

    /**
     * @inheritDoc
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->getBaseUrl() . '/user/self';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultScopes()
    {
        return ['user.info'];
    }

    /**
     * @inheritDoc
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException(
                $data['error'] ?: $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response
            );
        }
    }

    /**
     * @inheritDoc
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new RockebeansResourceOwner($response);
    }
}
