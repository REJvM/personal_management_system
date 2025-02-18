<?php

namespace App\Entity;

use App\Repository\BlogPostLinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogPostLinkRepository::class)]
class BlogPostLink
{
    public const LINK_ICONS = [
        'Github' => 'icon-github-mark',
        'Codepen' => 'icon-codepen',
        'Behance' => 'icon-behance2',
        'Youtube' => 'icon-youtube',
        'Facebook' => 'icon-facebook2',
        'X' => 'icon-x'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $url;

    #[ORM\Column(length: 255)]
    private ?string $icon = null;

    #[ORM\ManyToOne(inversedBy: 'links')]
    private ?BlogPost $blogPost = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(?BlogPost $blogPost): static
    {
        $this->blogPost = $blogPost;

        return $this;
    }
}
