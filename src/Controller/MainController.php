<?php

namespace App\Controller;

use App\Entity\User;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class MainController extends AbstractController {
    /**
     * Homepage
     *
     * @Route("/", name="app_homepage")
     */
    public function homepage(RouterInterface $router, ClientRegistry $clientRegistry)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser) {
            $loginUrl = $router->generate('connect_rocketbeans_start');
            return $this->render('frontpage_logged_out.html.twig', [
                'login_url' => $loginUrl,
            ]);
        }

        $rocketbeansClient = $clientRegistry->getClient('rocketbeans');

        return $this->render('frontpage.html.twig', [
            'user' => $currentUser,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }
}
