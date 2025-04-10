<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
#[UniqueEntity(fields: ['title'], message: 'The blog post title has to be unique.')]
class BlogPost
{
    public const CATEGORY_PROJECTS = 'projects';
    public const CATEGORY_AREA = 'area';
    public const CATEGORY_RESOURCES = 'resources';
    public const CATEGORY_ARCHIVE = 'archive';

    public const AVAILABLE_CATEGORIES = [
        self::CATEGORY_PROJECTS => self::CATEGORY_PROJECTS,
        self::CATEGORY_AREA => self::CATEGORY_AREA,
        self::CATEGORY_RESOURCES => self::CATEGORY_RESOURCES,
        self::CATEGORY_ARCHIVE => self::CATEGORY_ARCHIVE
    ];

    public const CATEGORY_ICONS = [
        self::CATEGORY_PROJECTS => "icon-rocket",
        self::CATEGORY_AREA => "icon-earth",
        self::CATEGORY_RESOURCES => "icon-lab",
        self::CATEGORY_ARCHIVE => "icon-drawer"
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdOn = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $modifiedOn = null;

    #[ORM\ManyToOne(inversedBy: 'createdBlogPosts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'modifiedBlogPosts')]
    private ?User $modifiedBy = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(
        choices: self::AVAILABLE_CATEGORIES,
        message: 'Choose a valid category.',
    )]
    private ?string $category = null;

    /**
     * @var Collection<int, BlogPostLink>
     */
    #[ORM\OneToMany(targetEntity: BlogPostLink::class, mappedBy: 'blogPost')]
    private Collection $links;

    #[ORM\ManyToOne]
    private ?FileUpload $image = null;

    public function __construct()
    {
        $this->links = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent()
    {
        return $this->content == null ? $this->content : stream_get_contents($this->content);
    }

    public function setContent($content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): static
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getModifiedOn(): ?\DateTimeInterface
    {
        return $this->modifiedOn;
    }

    public function setModifiedOn(?\DateTimeInterface $modifiedOn): static
    {
        $this->modifiedOn = $modifiedOn;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getModifiedBy(): ?User
    {
        return $this->modifiedBy;
    }

    public function setModifiedBy(?User $modifiedBy): static
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, BlogPostLink>
     */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function addLink(BlogPostLink $link): static
    {
        if (!$this->links->contains($link)) {
            $this->links->add($link);
            $link->setBlogPost($this);
        }

        return $this;
    }

    public function removeLink(BlogPostLink $link): static
    {
        if ($this->links->removeElement($link)) {
            // set the owning side to null (unless already changed)
            if ($link->getBlogPost() === $this) {
                $link->setBlogPost(null);
            }
        }

        return $this;
    }

    public function getImage(): ?FileUpload
    {
        return $this->image;
    }

    public function setImage(?FileUpload $image): static
    {
        $this->image = $image;

        return $this;
    }
}
