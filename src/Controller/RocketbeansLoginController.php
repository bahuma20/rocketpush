<?php

namespace App\Controller;


use App\OauthProviders\RockebeansResourceOwner;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RocketbeansLoginController extends AbstractController {
    /**
     * Link to this controller to start the connect process.
     *
     * @Route("/connect/rocketbeans", name="connect_rocketbeans_start")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('rocketbeans')
            ->redirect([
                'user.subscriptions.read',
            ]);
    }

    /**
     * @Route("/connect/rocketbeans/redirect", name="connect_rocketbeans_redirect")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry) {

    }
}
