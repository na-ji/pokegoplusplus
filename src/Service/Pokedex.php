<?php

namespace App\Service;


class Pokedex
{
    /**
     * @var array
     */
    public $pokedex;

    public function __construct()
    {
        $file = file_get_contents(__DIR__.'/../../public/assets/pokedex.json');
        $this->pokedex = json_decode($file, $assoc = true);
    }

    public function getName(int $pokedexNumber)
    {
        if (!array_key_exists($pokedexNumber, $this->pokedex)) {
            return '';
        }

        return $this->pokedex[$pokedexNumber]['names']['uk'];
    }
}
