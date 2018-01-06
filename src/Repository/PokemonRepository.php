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
            ->orderBy('pokemon.despawnTime', 'ASC')
            ->setParameter('now', $now, Type::DATETIME)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param $lat
     * @param $lng
     * @param int $pokemonId
     * @param \DateTime $despawnTime
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function pokemonExists($lat, $lng, int $pokemonId, \DateTime $despawnTime)
    {
        $queryBuilder = $this->createQueryBuilder('pokemon');
        $queryBuilder
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('pokemon.pokedexNumber', $pokemonId),
                    $queryBuilder->expr()->eq('pokemon.lat', round($lat, 6)),
                    $queryBuilder->expr()->eq('pokemon.lng', round($lng, 6)),
                    $queryBuilder->expr()->eq('pokemon.despawnTime', ':despawnTime')
                )
            )
            ->setParameter('despawnTime', $despawnTime, Type::DATETIME)
            ->setMaxResults(1)
        ;

        $pokemon = $queryBuilder->getQuery()->getOneOrNullResult();

        return $pokemon instanceof Pokemon;
    }

    /**
     * @param $lat
     * @param $lng
     * @param int $pokemonId
     * @param \DateTime $despawnTime
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function pokemonDoesNotExist($lat, $lng, int $pokemonId, \DateTime $despawnTime)
    {
        return ! $this->pokemonExists($lat, $lng, $pokemonId, $despawnTime);
    }
}
