<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PokemonRepository")
 */
class Pokemon
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
     * @ORM\Column(type="decimal", precision=8, scale=6)
     *
     * @var string
     */
    private $lat;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=6)
     *
     * @var string
     */
    private $lng;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Feed", inversedBy="pokemons")
     *
     * @var Feed
     */
    private $feed;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $despawnTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    private $pokedexNumber;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=1, nullable=true)
     *
     * @var string
     */
    private $iv;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    private $cp;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    private $level;

    public function __construct()
    {
        $this->despawnTime = new \DateTime('now');
        $this->despawnTime->modify('+1 hours');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLat(): ?string
    {
        return $this->lat;
    }

    /**
     * @param string $lat
     * @return Pokemon
     */
    public function setLat(string $lat): Pokemon
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * @return string
     */
    public function getLng(): ?string
    {
        return $this->lng;
    }

    /**
     * @param string $lng
     * @return Pokemon
     */
    public function setLng(string $lng): Pokemon
    {
        $this->lng = $lng;
        return $this;
    }

    /**
     * @return Feed
     */
    public function getFeed(): ?Feed
    {
        return $this->feed;
    }

    /**
     * @param Feed $feed
     */
    public function setFeed(Feed $feed): void
    {
        $this->feed = $feed;
    }

    /**
     * @return \DateTime
     */
    public function getDespawnTime(): \DateTime
    {
        return $this->despawnTime;
    }

    /**
     * @param \DateTime $despawnTime
     * @return Pokemon
     */
    public function setDespawnTime(\DateTime $despawnTime): Pokemon
    {
        $this->despawnTime = $despawnTime;

        return $this;
    }

    /**
     * @return int
     */
    public function getPokedexNumber(): ?int
    {
        return $this->pokedexNumber;
    }

    /**
     * @param int $pokedexNumber
     * @return Pokemon
     */
    public function setPokedexNumber(int $pokedexNumber): Pokemon
    {
        $this->pokedexNumber = $pokedexNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getIv(): ?string
    {
        return $this->iv;
    }

    /**
     * @param string $iv
     * @return Pokemon
     */
    public function setIv(string $iv): Pokemon
    {
        $this->iv = $iv;
        return $this;
    }

    /**
     * @return int
     */
    public function getCp(): ?int
    {
        return $this->cp;
    }

    /**
     * @param int $cp
     * @return Pokemon
     */
    public function setCp(int $cp): Pokemon
    {
        $this->cp = $cp;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * @param int $level
     * @return Pokemon
     */
    public function setLevel(int $level): Pokemon
    {
        $this->level = $level;

        return $this;
    }
}
