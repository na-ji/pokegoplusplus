<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Entity\Pokemon;
use App\Form\FeedType;
use App\Form\PokemonType;
use App\Repository\FeedRepository;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FeedController extends Controller
{
    /**
     * @Route("/", name="feed_index")
     *
     * @param EntityManagerInterface $entityManager
     * @param FeedRepository $feedRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(EntityManagerInterface $entityManager, FeedRepository $feedRepository, Request $request)
    {
        $feeds = $feedRepository->findAll();

        $form = $this->createForm(FeedType::class, new Feed());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feed = $form->getData();

            $entityManager->persist($feed);
            $entityManager->flush();

            return $this->redirectToRoute('feed_index');
        }

        return $this->render(
            'feed/index.html.twig',
            [
                'form' => $form->createView(),
                'feeds' => $feeds,
            ]
        );
    }

    /**
     * @Route("/{slug}", name="feed_show")
     *
     * @param Feed $feed
     * @param EntityManagerInterface $entityManager
     * @param PokemonRepository $pokemonRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Feed $feed, EntityManagerInterface $entityManager, PokemonRepository $pokemonRepository, Request $request)
    {
        $pokemons = $pokemonRepository->findBy(['feed' => $feed->getId()], ['despawnTime' => 'DESC']);

        $form = $this->createForm(PokemonType::class, new Pokemon());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Pokemon $pokemon */
            $pokemon = $form->getData();
            $pokemon->setFeed($feed);

            $entityManager->persist($pokemon);
            $entityManager->flush();

            return $this->redirectToRoute('feed_show', ['slug' => $feed->getSlug()]);
        }

        return $this->render(
            'feed/show.html.twig',
            [
                'form' => $form->createView(),
                'feed' => $feed,
                'pokemons' => $pokemons,
            ]
        );
    }
}
