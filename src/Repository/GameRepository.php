<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Game;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class GameRepository extends ServiceEntityRepository{
    public function __construct(ManagerRegistry$registry)
    {
        //Indique que le repository est associé à l'entité Game
        parent::__construct($registry, Game::class);
    }

    private function addJoin(QueryBuilder $qb) : void{
        $qb ->addSelect('i, s, u, c')
        ->leftJoin('g.image', 'i')
        ->leftJoin('g.support', 's')
        ->leftJoin('g.user', 'u')
        ->leftJoin('g.categories', 'c')
        ;
    }

    public function findPagination(int $page = 1, int $itemCount = 20, string $search): Paginator
    {
        $begin = ($page - 1) * $itemCount;

        $qb = $this->createQueryBuilder('g')
            ->setMaxResults($itemCount)
            -> setFirstResult($begin)
            ;

            if ('' !== $search)
            {
                $qb->where('g.title LIKE :search')
                ->setParameter(':search', '%'.$search.'%')
                ;
            }

        $this->addJoin($qb);

        return new Paginator($qb->getQuery());
    }

    public function findAll()
    {
        $qb = $this->createQueryBuilder('g');
        $this->addJoin($qb);
        ;
        return $qb->getQuery()->getResult();
    }

    public function findEnabled()
    {
        $qb = $this->createQueryBuilder('g');
        $this->addJoin($qb);
        $qb->where('g.enabled = true')
        ;
        return $qb->getQuery()->getResult();
    }

    public function findByUser(User $user): array{
        $qb = $this->createQueryBuilder('g');
        $this->addJoin($qb);
        $qb->where('g.user = :user')
        ->setParameter(':user', $user)
        ;
        return $qb->getQuery()->getResult();
    }

    
}