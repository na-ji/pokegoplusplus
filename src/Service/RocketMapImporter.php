<?php

namespace App\Service;

use App\Entity\Feed;
use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;

class RocketMapImporter
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PokemonRepository
     */
    private $pokemonRepository;

    /**
     * RocketMapImporter constructor.
     * @param EntityManagerInterface $entityManager
     * @param PokemonRepository $pokemonRepository
     */
    public function __construct(EntityManagerInterface $entityManager, PokemonRepository $pokemonRepository)
    {
        $this->entityManager = $entityManager;
        $this->pokemonRepository = $pokemonRepository;
    }

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbName
     * @param int $pokedexNumber
     * @param Feed $feed
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function importFromDatabase(
        string $host,
        string $user,
        string $password,
        string $dbName,
        int $pokedexNumber,
        Feed $feed
    ): string
    {
        $dsn = "mysql:host=$host;dbname=$dbName";
        $opt = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new \PDO($dsn, $user, $password, $opt);
        $now = new \DateTime('now', new \DateTimeZone('UTC'));

        $stmt = $pdo->prepare('SELECT * FROM `pokemon` WHERE `pokemon_id` = :pokemonId AND disappear_time > :disappearTime');
        $stmt->execute([
            ':pokemonId' => $pokedexNumber,
            ':disappearTime' => $now->format('Y-m-d H:i:s'),
        ]);

        $pokemonsCollection = $stmt->fetchAll();
        $numberOfPokemonImported = 0;

        foreach ($pokemonsCollection as $pokemonToImport) {
            $despawnTime = new \DateTime($pokemonToImport['disappear_time'], new \DateTimeZone('UTC'));
            $despawnTime->setTimezone(new \DateTimeZone('Europe/paris'));

            if ($this->pokemonRepository->pokemonExists(
                $pokemonToImport['latitude'],
                $pokemonToImport['longitude'],
                $pokemonToImport['pokemon_id'],
                $despawnTime
            )) {
                continue;
            }

            $ivPerfection = null;
            if ($pokemonToImport['individual_attack'] && $pokemonToImport['individual_defense'] && $pokemonToImport['individual_stamina']) {
                $ivPerfection = round(
                    ($pokemonToImport['individual_attack'] + $pokemonToImport['individual_defense'] + $pokemonToImport['individual_stamina']) / 45 * 100,
                    1
                );
            }

            $pokemon = new Pokemon();
            $pokemon
                ->setPokedexNumber($pokemonToImport['pokemon_id'])
                ->setLat($pokemonToImport['latitude'])
                ->setLng($pokemonToImport['longitude'])
                ->setDespawnTime($despawnTime)
                ->setFeed($feed)
                ->setCp($pokemonToImport['cp'])
                ->setIv($ivPerfection)
            ;

            $this->entityManager->persist($pokemon);
            $numberOfPokemonImported++;
        }

        $this->entityManager->flush();

        return sprintf("%d/%d pokemons imported", $numberOfPokemonImported, count($pokemonsCollection));
    }
}
