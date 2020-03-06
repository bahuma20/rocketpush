<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSubscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController {
    /**
     * Homepage
     *
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser) {
            return $this->render('frontpage_logged_out.html.twig');
        }

        $subscriptions = $currentUser->getUserSubscriptions();
        $subscriptions = $subscriptions->toArray();

        usort($subscriptions, function(UserSubscription $a, UserSubscription $b) {
            return strcmp($a->getTitle(), $b->getTitle());
        });

        return $this->render('frontpage.html.twig', [
            'user' => $currentUser,
            'showSubscriptions' => $subscriptions,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/impressum", name="app_impressum")
     */
    public function impressum()
    {
        return $this->render('impressum.html.twig');
    }

    /**
     * @Route("/datenschutz", name="app_datenschutz")
     */
    public function datenschutz()
    {
        return $this->render('datenschutz.html.twig');
    }
}
