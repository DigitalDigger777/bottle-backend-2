<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BottleRepository")
 */
class Bottle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $sex;

    /**
     * @ORM\Column(type="integer")
     */
    private $age_start;

    /**
     * @ORM\Column(type="integer")
     */
    private $age_end;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="bottles")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    private $product;

    public function getId()
    {
        return $this->id;
    }

    public function getSex(): ?int
    {
        return $this->sex;
    }

    public function setSex(int $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getAgeStart(): ?int
    {
        return $this->age_start;
    }

    public function setAgeStart(int $age_start): self
    {
        $this->age_start = $age_start;

        return $this;
    }

    public function getAgeEnd(): ?int
    {
        return $this->age_end;
    }

    public function setAgeEnd(int $age_end): self
    {
        $this->age_end = $age_end;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }
}
