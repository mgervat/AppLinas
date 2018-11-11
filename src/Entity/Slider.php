<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SliderRepository")
 */
class Slider
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $text1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $text2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $text3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $text4;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $text5;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $text6;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage1(): ?string
    {
        return $this->image1;
    }

    public function setImage1(?string $image1): self
    {
        $this->image1 = $image1;

        return $this;
    }

    public function getImage2(): ?string
    {
        return $this->image2;
    }

    public function setImage2(?string $image2): self
    {
        $this->image2 = $image2;

        return $this;
    }

    public function getText1(): ?string
    {
        return $this->text1;
    }

    public function setText1(?string $text1): self
    {
        $this->text1 = $text1;

        return $this;
    }

    public function getText2(): ?string
    {
        return $this->text2;
    }

    public function setText2(?string $text2): self
    {
        $this->text2 = $text2;

        return $this;
    }

    public function getText3(): ?string
    {
        return $this->text3;
    }

    public function setText3(?string $text3): self
    {
        $this->text3 = $text3;

        return $this;
    }

    public function getText4(): ?string
    {
        return $this->text4;
    }

    public function setText4(?string $text4): self
    {
        $this->text4 = $text4;

        return $this;
    }

    public function getText5(): ?string
    {
        return $this->text5;
    }

    public function setText5(?string $text5): self
    {
        $this->text5 = $text5;

        return $this;
    }

    public function getText6(): ?string
    {
        return $this->text6;
    }

    public function setText6(?string $text6): self
    {
        $this->text6 = $text6;

        return $this;
    }
}
