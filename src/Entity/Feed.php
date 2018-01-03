<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FeedRepository")
 */
class Feed
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     *
     * @var string
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Pokemon", mappedBy="feed")
     *
     * @var ArrayCollection
     */
    private $pokemons;

    /**
     * Feed constructor.
     */
    public function __construct()
    {
        $this->slug = substr(str_shuffle(MD5(microtime())), 0, 5);
        $this->pokemons = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Feed
     */
    public function setName(string $name): Feed
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return Feed
     */
    public function setSlug(string $slug): Feed
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPokemons(): ArrayCollection
    {
        return $this->pokemons;
    }

    /**
     * @return array
     */
    public function getLivePokemons(): array
    {
        $pokemons = $this->pokemons->toArray();
        $now = new \DateTime('now');

        $pokemons = array_filter($pokemons, function (Pokemon $pokemon) use ($now) {
            return $pokemon->getDespawnTime() > $now;
        });

        usort($pokemons, function (Pokemon $a, Pokemon $b) {
           return $a->getDespawnTime() > $b->getDespawnTime();
        });

        return $pokemons;
    }

    /**
     * @param ArrayCollection $pokemons
     * @return Feed
     */
    public function setPokemons(ArrayCollection $pokemons): Feed
    {
        $this->pokemons = $pokemons;

        return $this;
    }

    /**
     * @param Pokemon $pokemon
     * @return Feed
     */
    public function addPokemon(Pokemon $pokemon): Feed
    {
        if (!$this->pokemons->contains($pokemon)) {
            $this->pokemons->add($pokemon);
        }

        return $this;
    }

    /**
     * @param Pokemon $pokemon
     * @return Feed
     */
    public function removePokemon(Pokemon $pokemon): Feed
    {
        if ($this->pokemons->contains($pokemon)) {
            $this->pokemons->removeElement($pokemon);
        }

        return $this;
    }
}
