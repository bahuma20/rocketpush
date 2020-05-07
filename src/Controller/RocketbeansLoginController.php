<?php

namespace App\Controller;


use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
                'user.info',
                'user.subscriptions.read',
            ]);
    }

    /**
     * @Route("/connect/rocketbeans/redirect", name="connect_rocketbeans_redirect")
     */
    public function connectCheckAction() {

    }
}
