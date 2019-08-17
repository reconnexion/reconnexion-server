<?php

namespace App\Command;

use App\Entity\IncomingWebhook;
use AV\ActivityPubBundle\DbType\ActorType;
use AV\ActivityPubBundle\Entity\Actor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddApplicationCommand extends Command
{
    protected static $defaultName = 'app:create-incoming-webhook';

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    public function configure()
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED);
    }

    public function generateRandomString(int $length): string
    {
        return str_pad(dechex(random_int(0, 0xFFFFFFFFFFFF)), $length, '0', STR_PAD_LEFT);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $apiKey = $this->generateRandomString(16);

        $actor = new Actor();
        $actor
            ->setType(ActorType::APPLICATION)
            ->setUsername($username)
            ->setName('Incoming webhook');

        $incomingWebhook = new IncomingWebhook($actor);
        $incomingWebhook->setApiKey($apiKey);

        $this->em->persist($incomingWebhook);
        $this->em->flush();

        $output->writeln([
            "Generated a new incoming webhook with username : $username",
            "API key for this webhook : $apiKey"
        ]);
    }
}
