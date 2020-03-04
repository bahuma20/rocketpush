<?php

namespace App\Command;

use App\Entity\ScheduleItem;
use App\Entity\User;
use App\Service\RbtvApiService;
use App\Service\SyncService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckSchedule extends Command {
    private $api;
    private $em;

    protected static $defaultName = 'app:check-schedule';

    public function __construct(RbtvApiService $api, EntityManagerInterface $em)
    {
        $this->api = $api;
        $this->em = $em;
        parent::__construct();
    }


    protected function configure() {

    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        /** @var User $masterUser */
        $masterUser = $this->em->createQueryBuilder()
            ->select('u')
            ->from('App:User', 'u')
            ->where('u.masterUser = true')
            ->getQuery()
            ->getSingleResult();

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
                print $scheduleItem->timeStart;
                print $startTimestamp;
                if ($startTimestamp < time() + 10*60) {
                    $upcomingShow = $scheduleItem;
                    break;
                }
            }

            if ($upcomingShow) {
                $scheduleItem = $this->em->getRepository(ScheduleItem::class)
                    ->findOneBy([
                        'rbtvId' => $upcomingShow->id,
                    ]);

                print_r($upcomingShow);
                print_r($scheduleItem);

                // We haven't notified the users about this
                if (!$scheduleItem) {
                    $scheduleItem = new ScheduleItem();
                    $scheduleItem->setRbtvId($upcomingShow->id);
                    $scheduleItem->setSent(true);
                    $this->em->persist($scheduleItem);
                    $this->em->flush();

                    // TODO: Notify users about this show
                }
            }
        }

        return 0;
    }
}
