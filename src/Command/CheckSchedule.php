<?php

namespace App\Command;

use App\Entity\ScheduleItem;
use App\Entity\User;
use App\Service\RbtvApiService;
use App\Service\SyncService;
use BenTools\WebPushBundle\Model\Message\PushNotification;
use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionManagerInterface;
use BenTools\WebPushBundle\Sender\PushMessagerSenderInterface;
use BenTools\WebPushBundle\Sender\PushMessageSender;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckSchedule extends Command {
    private $api;
    private $em;
    private $userSubscriptionManager;
    private $sender;

    protected static $defaultName = 'app:check-schedule';

    public function __construct(RbtvApiService $api, EntityManagerInterface $em, UserSubscriptionManagerInterface $userSubscriptionManager, PushMessageSender $sender)
    {
        $this->api = $api;
        $this->em = $em;
        $this->userSubscriptionManager = $userSubscriptionManager;
        $this->sender = $sender;
        parent::__construct();
    }


    protected function configure() {

    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        /** @var User $masterUser */
        try {
            $masterUser = $this->em->createQueryBuilder()
                ->select('u')
                ->from('App:User', 'u')
                ->where('u.masterUser = true')
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $output->write('ERROR: Master user could not be found. Please set "masterUser" in the database to true at the main user.');
            return 1;
        } catch (NonUniqueResultException $e) {
            $output->write('ERROR: Multiple Master users found. Make sure that "masterUser" is only assigned to the main user.');
            return 2;
        }

        $client = $this->api->getClient($masterUser);

        $response = $client->get('schedule/normalized', [
            'query' => [
                'startDay' => time(),
                'endDay' => time()+24*60*60,
            ],
        ]);

        if ($response->getStatusCode() === 200) {
            $body = json_decode($response->getBody()->getContents());

            $upcomingShow = false;

            foreach ($body->data[0]->elements as $scheduleItem) {
                $startTimestamp = strtotime($scheduleItem->timeStart);
                $endTimestamp = strtotime($scheduleItem->timeEnd);

                if ($scheduleItem->type == 'live' && $startTimestamp < time() + 10*60 && time() < $endTimestamp) {
                    $upcomingShow = $scheduleItem;
                    break;
                }
            }

            if ($upcomingShow) {
                $scheduleItem = $this->em->getRepository(ScheduleItem::class)
                    ->findOneBy([
                        'rbtvId' => $upcomingShow->id,
                    ]);

                // We haven't notified the users about this
                if (!$scheduleItem) {
                    $scheduleItem = new ScheduleItem();
                    $scheduleItem->setRbtvId($upcomingShow->id);
                    $scheduleItem->setSent(true);
                    $this->em->persist($scheduleItem);
                    $this->em->flush();


                    // Gather Audience
                    $users = $this->em->createQuery(
                        'SELECT u
                        FROM App\Entity\User u
                        LEFT JOIN u.userSubscriptions s
                        WHERE s.rbtvId = :showid'
                    )
                        ->setParameter('showid', $upcomingShow->showId)
                        ->execute();

                    // Send out the notifications.
                    $subscriptions = [];

                    foreach ($users as $user) {
                        $subscriptions += $this->userSubscriptionManager->findByUser($user);
                    }

                    $notification = new PushNotification('Live: ' . $upcomingShow->title, [
                        PushNotification::BODY => $upcomingShow->topic,
                        PushNotification::DATA => [
                            'link' => 'https://rocketbeans.tv',
                        ],
                        PushNotification::BADGE => '/assets/badge-96x96.png', // 96x96px
                        PushNotification::ICON => '/assets/logo-192x192.png', // 192x192
                        PushNotification::RENOTIFY => true,
                        'tag' => 'show_started', // Only the latest show will be shown as a notification. Because when a new show starts, the old notification is irrelevant.
                    ]);

                    $this->sender->setDefaultOptions([
                        'TTL' => 25*60, // If the push server wasn't able to deliver the notification within 25 minutes, it is not necessary anymore.
                    ]);

                    $responses = $this->sender->push($notification->createMessage(), $subscriptions);

                    foreach ($responses as $response) {
                        if ($response->isExpired()) {
                            $this->userSubscriptionManager->delete($response->getSubscription());
                        }
                    }
                }
            }
        }

        return 0;
    }
}
