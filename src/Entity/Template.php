<?php

namespace App\Entity;

use App\Annotation\Link;
use App\Repository\TemplateRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=TemplateRepository::class)
 * @Link(
 *     "self",
 *     route = "templates_get_item",
 *     params = {"id": "object.getId()"}
 * )
 * @Link(
 *     "owner",
 *     route = "users_get_item",
 *     params = {"id": "object.getOwner().getId()"}
 * )
 * @Link(
 *     "products",
 *     route = "templates_get_products_collection",
 *     params = {"id": "object.getId()"}
 * )
 */
class Template implements ApiEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("user")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @Assert\Length(min=3, max=20)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9\s]*$/",
     *     message="Name must contain only letters and numbers",
     * )
     * @ORM\Column(type="string", length=255)
     * @Groups("user")
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("user")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="templates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="template", cascade={"persist", "remove"})
     */
    private $products;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setTemplate($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getTemplate() === $this) {
                $product->setTemplate(null);
            }
        }

        return $this;
    }


}
