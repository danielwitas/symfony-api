<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy()
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 *
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\MaxDepth(1)
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(min=3, max=50)
     * @ORM\Column(type="string", length=255)
     * @Serializer\MaxDepth(1)
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     * @ORM\Column(type="integer")
     * @Serializer\MaxDepth(1)
     */
    private $kcal;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\MaxDepth(1)
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

    /**
     * @Serializer\MaxDepth(1)
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @Serializer\MaxDepth(1)
     */
    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
