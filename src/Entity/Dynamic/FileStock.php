<?php

namespace App\Entity\Dynamic;

use App\Entity\Dynamic\Personne;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class FileStock
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $path;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $pathAbsolute;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $pathPicto;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $pathAbsolutePicto;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $mimeType = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $size = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $originalName = null;

    #[ORM\OneToMany(mappedBy: 'persPhoto', targetEntity: Personne::class)]
    private Collection $personnesWithPhoto;

    #[ORM\OneToMany(mappedBy: 'persBadge', targetEntity: Personne::class)]
    private Collection $personnesWithBadge;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->personnesWithPhoto = new ArrayCollection();
        $this->personnesWithBadge = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getPathAbsolute(): ?string
    {
        return $this->pathAbsolute;
    }

    public function setPathAbsolute(string $pathAbsolute): self
    {
        $this->pathAbsolute = $pathAbsolute;
        return $this;
    }

    public function getPathPicto(): ?string
    {
        return $this->pathPicto;
    }

    public function setPathPicto(string $pathPicto): self
    {
        $this->pathPicto = $pathPicto;
        return $this;
    }

    public function getPathAbsolutePicto(): ?string
    {
        return $this->pathAbsolutePicto;
    }

    public function setPathAbsolutePicto(string $pathAbsolutePicto): self
    {
        $this->pathAbsolutePicto = $pathAbsolutePicto;
        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;
        return $this;
    }

    public function getPersonnesWithPhoto(): Collection
    {
        return $this->personnesWithPhoto;
    }

    public function addPersonneWithPhoto(Personne $personne): self
    {
        if (!$this->personnesWithPhoto->contains($personne)) {
            $this->personnesWithPhoto[] = $personne;
            $personne->setPersPhoto($this);
        }
        return $this;
    }

    public function removePersonneWithPhoto(Personne $personne): self
    {
        if ($this->personnesWithPhoto->removeElement($personne)) {
            if ($personne->getPersPhoto() === $this) {
                $personne->setPersPhoto(null);
            }
        }
        return $this;
    }

    public function getPersonnesWithBadge(): Collection
    {
        return $this->personnesWithBadge;
    }

    public function addPersonneWithBadge(Personne $personne): self
    {
        if (!$this->personnesWithBadge->contains($personne)) {
            $this->personnesWithBadge[] = $personne;
            $personne->setPersBadge($this);
        }
        return $this;
    }

    public function removePersonneWithBadge(Personne $personne): self
    {
        if ($this->personnesWithBadge->removeElement($personne)) {
            if ($personne->getPersBadge() === $this) {
                $personne->setPersBadge(null);
            }
        }
        return $this;
    }
}