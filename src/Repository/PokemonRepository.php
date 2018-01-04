<?php

namespace App\Repository;

use App\Entity\Feed;
use App\Entity\Pokemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Type;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    public function findLivePokemon(Feed $feed): array
    {
        $now = new \DateTime('now');
        $queryBuilder = $this->createQueryBuilder('pokemon');

        $queryBuilder
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('pokemon.feed', $feed->getId()),
                    $queryBuilder->expr()->eq('pokemon.hidden', 0),
                    $queryBuilder->expr()->gte('pokemon.despawnTime', ':now')
                )
            )
            ->orderBy('pokemon.despawnTime', 'DESC')
            ->setParameter('now', $now, Type::DATETIME)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
