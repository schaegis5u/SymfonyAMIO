<?php

namespace App\Repository;
use App\Entity\Game;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GameRepository extends ServiceEntityRepository{
    public function __construct(ManagerRegistry$registry)
    {
        //Indique que le repository est associé à l'entité Game
        parent::__construct($registry, Game::class);
    }

    public function findAll()
    {
        $qb = $this->createQueryBuilder('g')
            ->addSelect('i, s, u, c')
            ->leftJoin('g.image', 'i')
            ->leftJoin('g.support', 's')
            ->leftJoin('g.user', 'u')
            ->leftJoin('g.categories', 'c')
        ;
        return $qb->getQuery()->getResult();
    }

    public function findEnabled()
    {
        $qb = $this->createQueryBuilder('g')
            ->addSelect('i, s, u')
            ->leftJoin('g.image', 'i')
            ->leftJoin('g.support', 's')
            ->leftJoin('g.user', 'u')
            ->leftJoin('g.categories', 'c')
            ->where('g.enabled = true')
        ;
        return $qb->getQuery()->getResult();
    }

    public function findByUser(User $user): array{
        $qb = $this->createQueryBuilder('g')
            ->addSelect('i, s, u')
            ->leftJoin('g.image', 'i')
            ->leftJoin('g.support', 's')
            ->leftJoin('g.user', 'u')
            ->leftJoin('g.categories', 'c')
            ->where('g.user = :user')
            ->setParameter(':user', $user)
        ;
        return $qb->getQuery()->getResult();
    }

    
}