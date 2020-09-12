<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This e-mail already exists.",
 *     groups={"registration"}
 * )
 * @UniqueEntity(
 *     fields={"username"},
 *     message="This username already exists.",
 *     groups={"registration"}
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("user")
     */
    private $id;

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\NotNull(groups={"registration"})
     * @Assert\Length(min=3, max=50)
     * @ORM\Column(type="string", length=255)
     * @Groups("user")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\NotNull(groups={"registration"})
     * @Assert\Email(groups={"registration"})
     * @Groups("user")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups("user")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long and contain at least one digit, one upper case letter and one lower case letter",
     *     groups={"registration"}
     * )
     * @Assert\NotBlank(groups={"registration", "change-password"})
     * @Assert\Length(min=3, max=50)
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getRepeatPassword()",
     *     message="Passwords do not match",
     *     groups={"registration"}
     *     )
     */
    private $repeatPassword;

    /**
     * @Assert\NotBlank(groups={"change-password"})
     * @Assert\Regex(
     *     groups={"change-password"},
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long and contain at least one digit, one upper case letter and one lower case letter",
     * )
     */
    private $newPassword;
    /**
     * @Assert\NotBlank(groups={"change-password"})
     * @Assert\Expression(
     *     "this.getNewPassword() === this.getRepeatNewPassword()",
     *     message="Passwords do not match",
     *     groups={"registration"}
     *     )
     */
    private $repeatNewPassword;


    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="owner", cascade={"persist", "remove"})
     */
    private $products;



    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): ?self
    {
        $this->username = $username;
        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getRepeatNewPassword(): ?string
    {
        return $this->repeatNewPassword;
    }

    public function setRepeatNewPassword(?string $repeatNewPassword): self
    {
        $this->repeatNewPassword = $repeatNewPassword;

        return $this;
    }

    public function getRepeatPassword(): ?string
    {
        return $this->repeatPassword;
    }

    public function setRepeatPassword(?string $repeatPassword): self
    {
        $this->repeatPassword = $repeatPassword;
        return $this;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setOwner($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getOwner() === $this) {
                $product->setOwner(null);
            }
        }

        return $this;
    }

}
