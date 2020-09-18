<?php

namespace App\Entity;

use App\Annotation\Link;
use App\Repository\ProductRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @Link(
 *     "self",
 *     route = "products_get_item",
 *     params = {"id": "object.getId()"}
 * )
 * @Link(
 *     "owner",
 *     route = "users_get_item",
 *     params = {"id": "object.getOwner().getId()"}
 * )
 */
class Product implements ApiEntityInterface
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
     * @Assert\Length(min=3, max=20)
     * @Assert\Type(type="alnum")
     * @ORM\Column(type="string", length=255)
     * @Groups("user")
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\Length(max=20)
     * @Assert\Type(type="numeric")
     * @ORM\Column(type="integer")
     * @Groups("user")
     */
    private $kcal;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("user")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Template::class, inversedBy="products")
     */
    private $template;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }


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

    public function getCreatedAt(): string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }


    public function getOwner(): ?UserInterface
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;

        return $this;
    }


}
