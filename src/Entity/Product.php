<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("user")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(min=3, max=50)
     * @ORM\Column(type="string", length=255)
     * @Groups("user")
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     * @ORM\Column(type="integer")
     * @Groups("user")
     */
    private $kcal;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="products", cascade="remove")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getKcal(): ?int
    {
        return $this->kcal;
    }

    public function setKcal(?int $kcal): self
    {
        $this->kcal = $kcal;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }


}
