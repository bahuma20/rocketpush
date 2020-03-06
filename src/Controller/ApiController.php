<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\WebPushUserSubscription;
use App\Service\SyncService;
use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionManagerInterface;
use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use http\Url;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiController extends AbstractController
{
    private $registry;
    private $em;
    /**
     * @var SyncService
     */
    private $syncService;

    /**
     * ApiController constructor.
     */
    public function __construct(UserSubscriptionManagerRegistry $registry, EntityManagerInterface $em, SyncService $syncService)
    {
        $this->registry = $registry;
        $this->em = $em;
        $this->syncService = $syncService;
    }


    /**
     * @Route("/api/subscription/info", name="subscription_info", methods={"POST"})
     */
    public function getSubscriptionInfo(Request $request, UserInterface $user)
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

    /**
     * @Route("/api/rbtv-subscriptions/sync", name="rbtvsubscriptions_sync", methods={"GET"})
     */
    public function syncRbtvSubscriptions(UserInterface $user) {
        $this->syncService->syncSubscriptionsOfUser($user);
        return new RedirectResponse('/');
    }
}
