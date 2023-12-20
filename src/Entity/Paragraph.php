<?php

namespace App\Entity;

use App\Repository\ParagraphRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ParagraphRepository::class)]
class Paragraph
{    

    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['paragraph'])]
    private ?int $id = null;

    #[ORM\Column(type:'text')]
    #[Groups(['paragraph'])]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'paragraphs')]
    #[ORM\JoinColumn(nullable:false)]
    #[Groups(['paragraph'])]
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
