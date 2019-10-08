<?php

namespace App\EventSubscriber;

use App\Service\PushService;
use App\Type\NotificationType;
use AV\ActivityPubBundle\DbType\ActivityType;
use AV\ActivityPubBundle\Entity\Activity;
use AV\ActivityPubBundle\Event\ActivityEvent;
use AV\ActivityPubBundle\Service\ActivityPubService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PushSubscriber implements EventSubscriberInterface
{
    protected $activityPubService;

    protected $pushService;

    public static function getSubscribedEvents()
    {
        return [
            ActivityEvent::NAME => 'onNewActivity',
        ];
    }

    public function __construct(ActivityPubService $activityPubService, PushService $pushService)
    {
        $this->activityPubService = $activityPubService;
        $this->pushService = $pushService;
    }

    public function onNewActivity(ActivityEvent $event)
    {
        switch($event->getActivity()->getType())
        {
            case ActivityType::CREATE:
                $this->onCreate($event->getActivity());
                break;
        }
    }

    public function onCreate(Activity $activity)
    {
        $receivingActors = $activity->getReceivingActors();
        $createdObject = $activity->getObject();

        foreach( $receivingActors as $actor ) {
            $this->pushService->notifyActor(
                $actor,
                "Nouvelle actualité postée par {$activity->getActor()->getName()}",
                [
                    'type' => NotificationType::CREATE_ACTIVITY,
                    'summary' => $activity->getSummary(),
                    'actorName' => $activity->getActor()->getName(),
                    'objectType' => $createdObject->getType(),
                    'url' => $this->activityPubService->getObjectUri($createdObject)
                ]
            );
        }
    }
}