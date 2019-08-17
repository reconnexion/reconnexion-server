<?php

namespace App\Entity;

use AV\ActivityPubBundle\Entity\Actor;
use AV\ActivityPubBundle\Entity\ActorUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="incoming_webhook")
 */
class IncomingWebhook extends ActorUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private $apiKey;

    public function __construct(Actor $actor)
    {
        parent::__construct($actor);
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {

    }

    public function getSalt()
    {

    }

    public function eraseCredentials()
    {

    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }
}