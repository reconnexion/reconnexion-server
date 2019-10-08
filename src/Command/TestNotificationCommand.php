<?php

namespace App\Command;

use App\Service\PushService;
use App\Type\NotificationType;
use AV\ActivityPubBundle\Entity\Actor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestNotificationCommand extends Command
{
    protected static $defaultName = 'app:test-notification';

    protected $em;

    protected $pushService;

    public function __construct(EntityManagerInterface $em, PushService $pushService)
    {
        $this->em = $em;
        $this->pushService = $pushService;

        parent::__construct();
    }

    public function configure()
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        $output->writeln("Sending notification to all devices of actor {$username}");

        $actorRepo = $this->em->getRepository(Actor::class);

        $actor = $actorRepo->findOneBy(['username' => $username]);

        $this->pushService->notifyActor(
            $actor,
            "Ceci est un test pour voir si la notification fonctionne",
            ['type' => NotificationType::ACTIVATION]
        );

        $output->writeln('OK!');
    }
}
