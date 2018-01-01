<?php

namespace App\Service;


class Pokedex
{
    public $pokedex;

    public function __construct()
    {
        $file = file_get_contents(__DIR__.'/../../public/assets/pokedex.json');
        $this->pokedex = json_decode($file);
    }
}
