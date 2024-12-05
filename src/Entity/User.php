<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const AVAILABLE_ROLES = [
        'Admin' => 'ROLE_ADMIN',
        'Guest' => 'ROLE_GUEST'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserProfile $userProfile = null;

    /**
     * @var Collection<int, BlogPost>
     */
    #[ORM\OneToMany(targetEntity: BlogPost::class, mappedBy: 'createdBy')]
    private Collection $createdBlogPosts;

    /**
     * @var Collection<int, BlogPost>
     */
    #[ORM\OneToMany(targetEntity: BlogPost::class, mappedBy: 'modifiedBy')]
    private Collection $modifiedBlogPosts;

    public function __construct()
    {
        $this->createdBlogPosts = new ArrayCollection();
        $this->modifiedBlogPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(?UserProfile $userProfile): static
    {
        // unset the owning side of the relation if necessary
        if ($userProfile === null && $this->userProfile !== null) {
            $this->userProfile->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($userProfile !== null && $userProfile->getUser() !== $this) {
            $userProfile->setUser($this);
        }

        $this->userProfile = $userProfile;

        return $this;
    }

    /**
     * @return Collection<int, BlogPost>
     */
    public function getCreatedBlogPosts(): Collection
    {
        return $this->createdBlogPosts;
    }

    public function addCreatedBlogPost(BlogPost $createdBlogPost): static
    {
        if (!$this->createdBlogPosts->contains($createdBlogPost)) {
            $this->createdBlogPosts->add($createdBlogPost);
            $createdBlogPost->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedBlogPost(BlogPost $createdBlogPost): static
    {
        if ($this->createdBlogPosts->removeElement($createdBlogPost)) {
            // set the owning side to null (unless already changed)
            if ($createdBlogPost->getCreatedBy() === $this) {
                $createdBlogPost->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlogPost>
     */
    public function getModifiedBlogPosts(): Collection
    {
        return $this->modifiedBlogPosts;
    }

    public function addModifiedBlogPost(BlogPost $modifiedBlogPost): static
    {
        if (!$this->modifiedBlogPosts->contains($modifiedBlogPost)) {
            $this->modifiedBlogPosts->add($modifiedBlogPost);
            $modifiedBlogPost->setModifiedBy($this);
        }

        return $this;
    }

    public function removeModifiedBlogPost(BlogPost $modifiedBlogPost): static
    {
        if ($this->modifiedBlogPosts->removeElement($modifiedBlogPost)) {
            // set the owning side to null (unless already changed)
            if ($modifiedBlogPost->getModifiedBy() === $this) {
                $modifiedBlogPost->setModifiedBy(null);
            }
        }

        return $this;
    }

}
