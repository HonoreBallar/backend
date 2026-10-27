<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Project implements PortfolioContent
{
    /** Categories used by the frontend project filter */
    public const CATEGORIES = ['Fullstack', 'IA', 'DevOps'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: self::CATEGORIES)]
    private ?string $category = 'Fullstack';

    /** @var list<string> */
    #[ORM\Column(type: Types::JSON)]
    private array $tags = [];

    /** Glow color of the card, as "r, g, b" */
    #[ORM\Column(length: 20)]
    #[Assert\Regex('/^\d{1,3}, ?\d{1,3}, ?\d{1,3}$/', message: 'Format attendu : "r, g, b" (ex. 139, 92, 246).')]
    private ?string $glow = '14, 165, 233';

    #[ORM\Column(length: 255)]
    private ?string $demoUrl = '#';

    #[ORM\Column(length: 255)]
    private ?string $sourceUrl = '#';

    #[ORM\Column]
    private bool $visible = true;

    #[ORM\Column]
    private int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    /** @return list<string> */
    public function getTags(): array
    {
        return $this->tags;
    }

    /** @param list<string>|null $tags */
    public function setTags(?array $tags): static
    {
        $this->tags = array_values($tags ?? []);

        return $this;
    }

    public function getGlow(): ?string
    {
        return $this->glow;
    }

    public function setGlow(?string $glow): static
    {
        $this->glow = $glow;

        return $this;
    }

    public function getDemoUrl(): ?string
    {
        return $this->demoUrl;
    }

    public function setDemoUrl(?string $demoUrl): static
    {
        $this->demoUrl = $demoUrl ?: '#';

        return $this;
    }

    public function getSourceUrl(): ?string
    {
        return $this->sourceUrl;
    }

    public function setSourceUrl(?string $sourceUrl): static
    {
        $this->sourceUrl = $sourceUrl ?: '#';

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): static
    {
        $this->visible = (bool) $visible;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position ?? 0;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->title;
    }
}
