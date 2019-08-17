<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getByUsername(string $username): ?User
    {
        return $this->createQueryBuilder('user')
            ->join('user.actor', 'actor')
            ->where('actor.username = :username')
            ->setParameters([
                'username' => $username
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}