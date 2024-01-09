<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ParagraphRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ParagraphRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['paragraph:read', 'article:read']],
)]
class Paragraph
{    

    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['paragraph:read', 'article:read'])]
    private ?int $id = null;

    #[ORM\Column(type:'text')]
    #[Groups(['paragraph:read', 'article:read'])]
    private ?string $content = null;
    
    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'paragraphs')]
    #[ORM\JoinColumn(nullable:false)]
    #[Groups(['paragraph:read'])]
    private ?Article $article = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }
}
