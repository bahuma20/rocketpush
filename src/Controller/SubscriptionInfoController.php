<?php

namespace App\Controller;

use App\Entity\WebPushUserSubscription;
use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionManagerInterface;
use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscriptionInfoController extends AbstractController
{
    private $registry;
    private $em;

    /**
     * SubscriptionInfoController constructor.
     */
    public function __construct(UserSubscriptionManagerRegistry $registry, EntityManagerInterface $em)
    {
        $this->registry = $registry;
        $this->em = $em;
    }


    /**
     * @Route("/api/subscription/info", name="subscription_info", methods={"POST"})
     */
    public function getInfoByEndpoint(Request $request, UserInterface $user)
    {
        $data = json_decode($request->getContent(), true);
        $endpoint = $data['endpoint'] ?? '';
        $manager = $this->registry->getManager($user);
        $subscriptionHash = $manager->hash($endpoint, $user);
        /** @var WebPushUserSubscription $subscription */
        $subscription = $manager->getUserSubscription($user, $subscriptionHash);

        if (!$subscription) {
            return new JsonResponse([
                'found' => FALSE,
            ]);
        }

        return new JsonResponse([
            'found' => TRUE,
            'label' => $subscription->getLabel() ?: 'Unbenanntes Gerät',
        ]);
    }

    /**
     * @Route("/api/subscription/change-label", name="subscription_change-label", methods={"POST"})
     */
    public function changeLabel(Request $request, UserInterface $user)
    {
        $data = json_decode($request->getContent(), true);
        $endpoint = $data['endpoint'] ?? '';
        $manager = $this->registry->getManager($user);
        $subscriptionHash = $manager->hash($endpoint, $user);
        /** @var WebPushUserSubscription $subscription */
        $subscription = $manager->getUserSubscription($user, $subscriptionHash);

        if (!$subscription) {
            return new BadRequestHttpException('No subscription found with this endpoint');
        }

        $subscription->setLabel(trim($data['label']));
        $this->em->persist($subscription);
        $this->em->flush();

        return new Response('Was updated');
    }
}
