<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
Use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
Use Symfony\Component\Validator\Constraints AS Assert;
use App\Repository\PaintingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaintingRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    fields: ['title'], message: 'Ce tableau existe déjà',)]

class Painting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Votre titre doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Votre titre ne peux pas depasser {{ limit }} caractères',

    )]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Votre nom et prénom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Votre nom et prénom ne peux pas dépasser {{ limit }} caractères',

    )]
    private ?string $author = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $makedAt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 10,
        minMessage: 'Votre description doit contenir au moins {{ limit }} caractères',
    )]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Length(
        min: 1,
        minMessage: 'Votre hauteur doit contenir au moins {{ limit }} cm',
    )]
    private ?float $height = null;

    #[ORM\Column]
    #[Assert\Length(
        min: 1,
        minMessage: 'Votre largeur doit contenir au moins {{ limit }} cm',
    )]
    private ?float $width = null;

    #[ORM\Column(length: 255)]
    #[Assert\Image(
        mimeTypes: ['image/png','image/jpg','image/jpeg', 'image/webp'],
        mimeTypesMessage: 'Le type d\'image "{{ type }}" est incorrecte. {{ types }} autorisés',
    )]
    private ?string $imageName = null;

    #[ORM\ManyToOne(inversedBy: 'paintings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'paintings')]
    private ?Technical $technical = null;

    #[ORM\OneToMany(mappedBy: 'painting', targetEntity: Comment::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $comments;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $isPublished = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getMakedAt(): ?\DateTimeImmutable
    {
        return $this->makedAt;
    }

    public function setMakedAt(\DateTimeImmutable $makedAt): self
    {
        $this->makedAt = $makedAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTechnical(): ?Technical
    {
        return $this->technical;
    }

    public function setTechnical(?Technical $technical): self
    {
        $this->technical = $technical;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPainting($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPainting() === $this) {
                $comment->setPainting(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function createSlug()
    {
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->title);
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
