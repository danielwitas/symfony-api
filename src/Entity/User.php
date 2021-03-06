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
use App\Annotation\Link;

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
 * @Link(
 *     "self",
 *     route = "users_get_item",
 *     params = {"id": "object.getId()"}
 * )
 * @Link(
 *     "products",
 *     route = "users_products",
 *     params = {"id": "object.getId()"}
 * )
 * * @Link(
 *     "templates",
 *     route = "users_templates",
 *     params = {"id": "object.getId()"}
 * )
 */
class User implements UserInterface, ApiEntityInterface
{

    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const DEFAULT_ROLES = [self::ROLE_USER];
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
     * @Assert\Length(min=3, max=20)
     * @ORM\Column(type="string", length=255)
     * @Groups("user")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(groups={"registration", "reset-password", "change-email"})
     * @Assert\NotNull(groups={"registration", "reset-password", "change-email"})
     * @Assert\Email(groups={"registration", "reset-password", "change-email"})
     * @Groups("admin")
     */
    private $email;

    /**
     * @Assert\NotBlank(groups={"change-role"})
     * @Assert\NotNull(groups={"change-role"})
     * @ORM\Column(type="json")
     * @Groups("user")
     *
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
     * @Assert\NotNull(groups={"registration", "change-password"})
     * @Assert\Length(min=7, max=50)
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\NotNull(groups={"registration"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getRepeatPassword()",
     *     message="Passwords do not match",
     *     groups={"registration"}
     *     )
     */
    private $repeatPassword;

    /**
     * @Assert\NotBlank(groups={"change-password"})
     * @Assert\NotNull(groups={"change-password"})
     * @Assert\Regex(
     *     groups={"change-password"},
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long and contain at least one digit, one upper case letter and one lower case letter",
     * )
     */
    private $newPassword;
    /**
     * @Assert\NotBlank(groups={"change-password"})
     * @Assert\NotNull(groups={"change-password"})
     * @Assert\Expression(
     *     "this.getNewPassword() === this.getRepeatNewPassword()",
     *     message="Passwords do not match",
     *     groups={"registration"}
     *     )
     */
    private $repeatNewPassword;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\OneToMany(targetEntity=Template::class, mappedBy="owner", cascade={"persist", "remove"})
     */
    private $templates;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="owner", cascade={"persist", "remove"})
     */
    private $products;


    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
        $this->enabled = false;
        $this->confirmationToken = null;
        $this->templates = new ArrayCollection();
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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
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

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function __toString(): string
    {
        return $this->username;
    }

    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    }

    public function setPasswordChangeDate($passwordChangeDate): void
    {
        $this->passwordChangeDate = $passwordChangeDate;
    }

    /**
     * @return Collection|Template[]
     */
    public function getTemplates(): Collection
    {
        return $this->templates;
    }

    public function addTemplate(Template $template): self
    {
        if (!$this->templates->contains($template)) {
            $this->templates[] = $template;
            $template->setOwner($this);
        }

        return $this;
    }

    public function removeTemplate(Template $template): self
    {
        if ($this->templates->contains($template)) {
            $this->templates->removeElement($template);
            // set the owning side to null (unless already changed)
            if ($template->getOwner() === $this) {
                $template->setOwner(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?UserInterface
    {
        return $this;
    }


}
