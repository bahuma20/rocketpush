<?php

namespace App\Command;

use App\Service\SyncService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncSubscribedShows extends Command {
    private $syncService;

    protected static $defaultName = 'app:sync-subscribed-shows';

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
        parent::__construct();
    }


    protected function configure() {

    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $errors = $this->syncService->syncSubscriptions();
        $output->write($errors);

        return 0;
    }
}
