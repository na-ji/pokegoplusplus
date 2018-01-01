<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Form\FeedType;
use App\Repository\FeedRepository;
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
}
