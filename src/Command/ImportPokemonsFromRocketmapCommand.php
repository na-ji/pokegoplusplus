<?php

namespace App\Command;

use App\Entity\Feed;
use App\Repository\FeedRepository;
use App\Service\Pokedex;
use App\Service\RocketMapImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportPokemonsFromRocketmapCommand extends Command
{
    protected static $defaultName = 'app:import-pokemons-from-rocketmap';

    /**
     * @var RocketMapImporter
     */
    private $rocketMapImporter;

    /**
     * @var FeedRepository
     */
    private $feedRepository;

    /**
     * @var Pokedex
     */
    private $pokedex;

    /**
     * ImportPokemonsFromRocketmapCommand constructor.
     * @param RocketMapImporter $rocketMapImporter
     * @param FeedRepository $feedRepository
     * @param Pokedex $pokedex
     */
    public function __construct(RocketMapImporter $rocketMapImporter, FeedRepository $feedRepository, Pokedex $pokedex)
    {
        parent::__construct();
        $this->rocketMapImporter = $rocketMapImporter;
        $this->feedRepository = $feedRepository;
        $this->pokedex = $pokedex;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setDescription('Import pokemon from a RocketMap database')
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'Mysql host')
            ->addOption('user', null, InputOption::VALUE_REQUIRED, 'Mysql user')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Mysql password')
            ->addOption('db-name', null, InputOption::VALUE_REQUIRED, 'Mysql database name')
            ->addOption('pokedex-number', null, InputOption::VALUE_REQUIRED, 'Pokedex number')
            ->addOption('feed-slug', null, InputOption::VALUE_REQUIRED, 'Feed slug')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $host = $input->getOption('host');
        $dbName = $input->getOption('db-name');
        $user = $input->getOption('user');
        $password = $input->getOption('password');
        $pokedexNumber = intval($input->getOption('pokedex-number'));
        $feedSlug = $input->getOption('feed-slug');

        if (!$host || !$dbName || !$user || !$password || !$pokedexNumber || !$feedSlug) {
            $io->error('Missing options');

            return;
        }

        /** @var Feed $feed */
        $feed = $this->feedRepository->findOneBy(['slug' => $feedSlug]);

        if (!$feed instanceof Feed) {
            $io->error('No feed found');

            return;
        }

        $output->writeln(sprintf('Importing pokemon #%d `%s`', $pokedexNumber, $this->pokedex->getName($pokedexNumber)));

        $message = $this->rocketMapImporter->importFromDatabase($host, $user, $password, $dbName, $pokedexNumber, $feed);

        $io->success($message);
    }
}
