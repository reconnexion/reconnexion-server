<?php

namespace App\Command;

use AV\ActivityPubBundle\DbType\ActorType;
use AV\ActivityPubBundle\Entity\Actor;
use AV\ActivityPubBundle\Service\ActivityPubService;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';

    protected $em;

    protected $activityPubService;

    protected $slugService;

    public function __construct(EntityManagerInterface $em, ActivityPubService $activityPubService, SlugifyInterface $slugify)
    {
        $this->em = $em;
        $this->activityPubService = $activityPubService;
        $this->slugService = $slugify;

        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /*
         * JSON FILE
         */

        $filenames = array();
        $finder = new Finder();
        $files = $finder->files()->in('imports')->name('*.json');

        foreach ($files as $file) {
            $filenames[] = $file->getFilename();
        }

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'What JSON file do you want to import ?',
            $filenames
        );
        $question->setErrorMessage('Filename %s is invalid.');
        $filename = $helper->ask($input, $output, $question);

        $selectedFile = __DIR__ . "/../../imports/" . $filename;
        $jsonActivities = json_decode(file_get_contents($selectedFile), true);

        /*
         * ACTOR
         */

        $question = new Question('Please enter the username of the actor who will import these files : ');
        $question->setValidator(function ($username) {
            $actor = $this->em->getRepository(Actor::class)->findOneBy(['username' => $username]);
            if (!$actor) throw new \RuntimeException('No actor found with this username');
            return $actor;
        });
        $question->setMaxAttempts(3);

        /** @var Actor $actor */
        $actor = $helper->ask($input, $output, $question);

        /*
         * CONFIRMATION
         */

        $question = new ConfirmationQuestion('This will import ' . count($jsonActivities) .  ' activities for actor ' . $actor->getName() . '. Continue ?', true);

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('<error>Interrupted!</error>');
            return;
        }

        /*
         * IMPORT
         */

        foreach( $jsonActivities as $jsonActivity ) {
            $output->writeln('Importing ' . $jsonActivity['name'] . '...');

            // Add attributedTo field if it doesn't exist
            if( !array_key_exists('attributedTo', $jsonActivity) ) {
                $jsonActivity['attributedTo'] = $this->activityPubService->getObjectUri($actor);
            }

            // Format username from name if this is an actor and no username is given
            if( ActorType::includes($jsonActivity['type']) && !array_key_exists('username', $jsonActivity) ) {
                $jsonActivity['username'] = $this->slugService->slugify($jsonActivity['name']);
            }

            $this->activityPubService->handleActivity($jsonActivity, $actor, true);
        }

        $output->writeln('OK!');
    }
}
