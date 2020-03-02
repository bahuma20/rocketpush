<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserSubscription;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;

class SyncService {

    private $rbtvApiService;
    private $em;

    public function __construct(RbtvApiService $rbtvApiService, EntityManagerInterface $em)
    {
        $this->rbtvApiService = $rbtvApiService;
        $this->em = $em;
    }

    public function syncSubscriptions()
    {

        $errors = [];

        /** @var User[] $users */
        $users = $this->em->getRepository(User::class)
            ->findAll();

        foreach ($users as $user) {
            $errors += $this->syncSubscriptionsOfUser($user);
        }

        return $errors;
    }

    public function syncSubscriptionsOfUser(User $user)
    {
        $errors = [];

        try {
            $client = $this->rbtvApiService->getClient($user);
            $response = $client->get('subscription/mysubscriptions');
            $body = json_decode($response->getBody()->getContents());

            if (!$body) {
                $errors[] = 'User ' . $user->getId() . ': Cannot decode response body.';
            } else {
                $this->em->createQueryBuilder()
                    ->delete('App:UserSubscription', 's')
                    ->where('s.user = :userid')
                    ->setParameter('userid', $user->getId())
                    ->getQuery()
                    ->execute();

                foreach ($body->data as $subscriptionType => $subscriptionGroup) {
                    // Only store shows at the moment
                    if ($subscriptionType == 'shows') {
                        foreach ($subscriptionGroup as $subscription) {
                            $sub = new UserSubscription();
                            $sub->setType('show');
                            $sub->setUser($user);
                            $sub->setRbtvId($subscription->id);
                            $sub->setTitle($subscription->title);

                            $this->em->persist($sub);
                        }
                    }
                }

                $user->setLastSync(time());
                $this->em->persist($user);

                $this->em->flush();
            }
        } catch (GuzzleException $e) {
            $errors[] = 'User '. $user->getId() .': GuzzleException: ' . $e->getMessage();
        }

        return $errors;
    }
}
