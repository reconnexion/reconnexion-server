<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="device")
 */
class Device
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", nullable=true, referencedColumnName="id")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(name="token", length=255, type="string")
     */
    protected $token;

    /**
     * @var array
     * @ORM\Column(name="latest_response", length=255, type="json", nullable=true)
     */
    protected $latestResponse;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Device
    {
        $this->id = $id;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Device
    {
        $this->user = $user;
        return $this;
    }

    public function getToken(): string
    {
        // Extract expo token
        preg_match('/^ExponentPushToken\[([^\]]*)\]$/', $this->token, $matches);
        if( $matches && count($matches) > 1 ) {
            return $matches[1];
        } else {
            return null;
        }
    }

    public function setToken(string $token): Device
    {
        $this->token = $token;
        return $this;
    }

    public function getLatestResponse(): array
    {
        return $this->latestResponse;
    }

    public function setLatestResponse(array $latestResponse): Device
    {
        $this->latestResponse = $latestResponse;
        return $this;
    }
}