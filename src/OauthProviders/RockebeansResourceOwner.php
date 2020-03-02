<?php

namespace App\OauthProviders;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class RockebeansResourceOwner implements ResourceOwnerInterface {

    protected $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }


    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->response['data']['id'] ?: null;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->response;
    }

    public function getDisplayName()
    {
        return $this->response['data']['displayName'] ?: null;
    }
}
