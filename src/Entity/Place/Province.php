<?php

namespace App\Entity\Place;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Province
 * @package App\Entity\Place
 *
 * @ORM\Entity(repositoryClass="App\Repository\Place\ProvinceRepository")
 */
class Province
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_real;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getNameReal()
    {
        return $this->name_real;
    }

    /**
     * @param mixed $name_real
     */
    public function setNameReal($name_real): void
    {
        $this->name_real = $name_real;
    }


}