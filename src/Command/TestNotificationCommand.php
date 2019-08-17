<?php

namespace App\Command;

use App\Entity\Device;
use App\Service\PushService;
use App\Type\NotificationType;
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
            ->addArgument('actionId', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $actionId = $input->getArgument('actionId');

        $output->writeln(['Sending notification to all devices about action :', $actionId, '']);

        $devicesRepo = $this->em->getRepository(Device::class);
        /** @var Device[] $devices */
        $devices = $devicesRepo->findAll();

        foreach($devices as $device) {
            $output->write('Sending to user ' . $device->getUser()->getUsername() . '...');

            $this->pushService->notifyDevice(
                $device,
                "Une nouvelle action a été ajoutée !",
                ['type' => NotificationType::CREATE_ACTIVITY, 'objectId' => $actionId]
            );

            $output->writeln('OK!');
        }
    }
}
