<?php

namespace App\Service;

use App\Entity\Device;
use App\Entity\User;
use App\Type\NotificationType;
use AV\ActivityPubBundle\Entity\Actor;
use Doctrine\ORM\EntityManagerInterface;
use Solvecrew\ExpoNotificationsBundle\Manager\NotificationManager;
use Solvecrew\ExpoNotificationsBundle\Model\NotificationContentModel;

class PushService
{
    private $em;

    private $expo;

    public function __construct(EntityManagerInterface $em, NotificationManager $notificationManager)
    {
        $this->em = $em;
        $this->expo = $notificationManager;
    }

    public function subscribe(User $user, string $deviceToken)
    {
        $deviceRepo = $this->em->getRepository(Device::class);
        $device = $deviceRepo->findOneBy(['token' => $deviceToken]);

        // If device was not registered yet
        if( !$device ) {
            $device = new Device();
            $device
                ->setToken($deviceToken)
                ->setUser($user);

            $this->em->persist($device);
            $this->em->flush();

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
        if( $device->getToken() ) {
            /** @var NotificationContentModel $notificationContentModel */
            $notificationContentModel = $this->expo->sendNotification($message, $device->getToken(), '', $data);

            $device->setLatestResponse([
                'wasSuccessful' => $notificationContentModel->getWasSuccessful(),
                'responseMessage' => $notificationContentModel->getResponseMessage(),
                'responseDetails' => $notificationContentModel->getResponseDetails(),
                'body' => $notificationContentModel->getBody(),
                'data' => $notificationContentModel->getData()
            ]);

            $this->em->flush();
        }
    }
}