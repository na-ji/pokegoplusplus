<?php

namespace App\Twig;

use App\Service\Pokedex;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PokedexExtension extends AbstractExtension
{
    /**
     * @var Pokedex
     */
    private $pokedex;

    public function __construct(Pokedex $pokedex)
    {
        $this->pokedex = $pokedex;
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('pokedexName', array($this, 'getPokedexName')),
        );
    }

    public function getPokedexName(int $pokedexNumber)
    {
        return $this->pokedex->getName($pokedexNumber);
    }
}
