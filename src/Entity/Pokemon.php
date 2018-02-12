<?php

namespace App\Entity;

use App\Service\Math;
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
     * @ORM\Column(type="decimal", precision=9, scale=6)
     *
     * @var string
     */
    private $lat;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=6)
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

    /**
     * @ORM\Column(type="boolean", options={"default"=0})
     *
     * @var bool
     */
    private $hidden = false;

    public function __construct(array $json = null)
    {
        $this->despawnTime = new \DateTime('now');
        $this->despawnTime->modify('+1 hours');

        if (!is_array($json)) {
            return;
        }

        if (array_key_exists('lat', $json)) {
            $this->setLat($json['lat']);
        }

        if (array_key_exists('lng', $json)) {
            $this->setLng($json['lng']);
        }

        if (array_key_exists('pokedexEntry', $json) && array_key_exists('Number', $json['pokedexEntry'])) {
            $this->setPokedexNumber($json['pokedexEntry']['Number']);
        }

        if (array_key_exists('iv', $json)) {
            $this->setIv($json['iv']);
        }

        if (array_key_exists('pc', $json)) {
            $this->setCp($json['pc']);
        }

        if (array_key_exists('lvl', $json)) {
            $this->setLevel($json['lvl']);
        }

        if (array_key_exists('despawn', $json) && $json['despawn']) {
            $despawnTime = \DateTime::createFromFormat('H:i', $json['despawn']);
            $now = new \DateTime('now');

            if ($despawnTime < $now) {
                $despawnTime->modify('+1 days');
            }

            $this->setDespawnTime($despawnTime);
        }
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
     * @return Pokemon
     */
    public function setFeed(Feed $feed): Pokemon
    {
        $this->feed = $feed;

        return $this;
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
     * @param string|null $iv
     * @return Pokemon
     */
    public function setIv(?string $iv): Pokemon
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
     * @param int|null $cp
     * @return Pokemon
     */
    public function setCp(?int $cp): Pokemon
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

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     * @return Pokemon
     */
    public function setHidden(bool $hidden): Pokemon
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * @return Pokemon
     */
    public function hide(): Pokemon
    {
        return $this->setHidden(true);
    }

    /**
     * @return string
     */
    public function getStats(): string
    {
        $stats = '';

        if ($this->getIv()) {
            $stats .= ' '.$this->getIv().'iv';
        }

        if ($this->getCp()) {
            $stats .= ' CP'.$this->getCp();
        }

        if ($this->getLevel()) {
            $stats .= ' LVL'.$this->getLevel();
        }

        return $stats;
    }

    /**
     * @param double|bool $lat
     * @param double|bool $lng
     * @return string
     */
    public function calculateDistance($lat, $lng): string
    {
        $text = '';

        if (!$lat || !$lng) {
            return $text;
        }

        $distance = Math::haversineGreatCircleDistance($this->lat, $this->lng, $lat, $lng);
        $text = 'Distance: '.$distance.'km';
        $text .= ' | Cooldown: '.Math::distanceToCooldown($distance);

        return $text;
    }
}
