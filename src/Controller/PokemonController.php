<?php

namespace App\Controller;

use App\Entity\Pokemon;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PokemonController extends Controller
{
    /**
     * @Route("/pokemon/{id}/hide", name="pokemon_hide")
     *
     * @param Pokemon $pokemon
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function index(Pokemon $pokemon, EntityManagerInterface $entityManager)
    {
        $pokemon->hide();
        $entityManager->flush();

        return new JsonResponse([
            'status' => 'OK',
        ]);
    }
}
