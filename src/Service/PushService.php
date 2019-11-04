<?php

namespace App\Service;

use App\Entity\Device;
use App\Entity\User;
use App\Type\NotificationType;
use AV\ActivityPubBundle\Entity\Actor;
use Doctrine\ORM\EntityManagerInterface;
use ExponentPhpSDK\Expo;
use Psr\Log\LoggerInterface;

class PushService
{
    private $em;

    private $expo;

    private $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        // TODO use https://github.com/solvecrew/ExpoNotificationsBundle
        $this->expo = Expo::normalSetup();
        $this->logger = $logger;
    }

    public function subscribe(User $user, string $deviceToken)
    {
        $newDevice = false;

        $deviceRepo = $this->em->getRepository(Device::class);
        $device = $deviceRepo->findOneBy(['token' => $deviceToken]);

        if( !$device ) {
            $device = new Device();
            $device
                ->setToken($deviceToken)
                ->setUser($user);

            $this->em->persist($device);
            $this->em->flush();

            $newDevice = true;
        }

        $result = $this->expo->subscribe('device_' . $device->getId(), $deviceToken);

        $this->logger->info('Subscribe result for device ' . $deviceToken . ' with ID device_' . $device->getId() . " : " . $result);

        if( $newDevice ) {
            $this->notifyDevice(
                $device,
                "Notifications activées avec succès !",
                ['type' => NotificationType::ACTIVATION]
            );
        }
    }

    // Notify all devices connected to the actor
    public function notifyActor(Actor $actor, string $message, array $data = [])
    {
        $userRepo = $this->em->getRepository(User::class);

        /** @var User $user */
        $user = $userRepo->findOneBy(['actor' => $actor]);

        // If an user is attached to this actor
        if( $user ) {
            $this->notifyUser($user, $message, $data);
        }
    }

    // Notify all devices connected to the user
    public function notifyUser(User $user, string $message, array $data = [])
    {
        $deviceRepo = $this->em->getRepository(Device::class);

        /** @var Device[] $devices */
        $devices = $deviceRepo->findBy(['user' => $user]);

        foreach( $devices as $device ) {
            $this->notifyDevice($device, $message, $data);
        }
    }

    public function notifyDevice(Device $device, string $message, array $data = [])
    {
        $notification = [
            'body' => $message
        ];

        if( count($data) > 0 ) {
            $notification['data'] = json_encode($data);
        }

        $this->expo->notify('device_' . $device->getId(), $notification);
    }
}