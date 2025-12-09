<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Dynamic\Profil\CreateProfilController;
use App\Controller\Dynamic\Profil\ListeProfilController;
use App\Controller\Dynamic\Profil\UpdateProfilController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

/**
 * Profil
 */
#[ORM\Table(name: 'profil')]
#[ORM\Index(name: 'WDIDX_Profil_PFL_AccesWeb', columns: ['PFL_AccesWeb'])]
#[ORM\Index(name: 'WDIDX_Profil_PFL_AccesMobile', columns: ['PFL_AccesMobile'])]
#[ORM\Index(name: 'WDIDX_Profil_PFL_Libelle', columns: ['PFL_Libelle'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class Profil
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\Column(name: 'ID_Profil', unique: true, type: 'string', length: 36, nullable: false)]
    private ?string $idProfil = null;

    #[ORM\Column(name: 'PFL_Libelle', type: 'string', length: 50, nullable: false)]
    private ?string $pflLibelle;

    #[ORM\Column(name: 'PFL_Slug', type: 'string', length: 50, nullable: false)]
    private ?string $pflSlug;

    #[ORM\Column(name: 'PFL_AccesWeb', type: 'boolean', nullable: false)]
    private ?bool $pflAccesweb = false;

    #[ORM\Column(name: 'PFL_AccesMobile', type: 'boolean', nullable: false)]
    private ?bool $pflAccesmobile = false;

    // ajout d'un champ pour le rang
    #[ORM\Column(name: 'PFL_Rang', type: 'integer', nullable: true)]
    private ?int $pflRang = 0;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $usercreation;

    // Add OneToMany relationship with Personne
    #[ORM\OneToMany(mappedBy: 'idProfil', targetEntity: Personne::class)]
    private Collection $personnes;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;
 
    public function __construct()
    {
        $this->personnes = new ArrayCollection();
        $this->idProfil = Uuid::v4()->toRfc4122();
    }

    public function getIdProfil(): ?string
    {
        return $this->idProfil;
    }

    public function getPflLibelle(): ?string
    {
        return $this->pflLibelle;
    }

    public function setPflLibelle(string $pflLibelle): self
    {
        $this->pflLibelle = $pflLibelle;
        return $this;
    }

    public function getPflSlug(): ?string
    {
        return $this->pflSlug;
    }

    public function setPflSlug(string $pflSlug): self
    {
        $this->pflSlug = $pflSlug;
        return $this;
    }

    public function isPflAccesweb(): ?bool
    {
        return $this->pflAccesweb;
    }

    public function setPflAccesweb(bool $pflAccesweb): self
    {
        $this->pflAccesweb = $pflAccesweb;
        return $this;
    }

    public function isPflAccesmobile(): ?bool
    {
        return $this->pflAccesmobile;
    }

    public function setPflAccesmobile(bool $pflAccesmobile): self
    {
        $this->pflAccesmobile = $pflAccesmobile;
        return $this;
    }

    public function getUsercreation(): ?Utilisateur
    {
        return $this->usercreation;
    }

    public function setUsercreation(?Utilisateur $usercreation): self
    {
        $this->usercreation = $usercreation;
        return $this;
    }

    // Add getters and setters for personnes
    public function getPersonnes(): Collection
    {
        return $this->personnes;
    }

    public function addPersonne(Personne $personne): self
    {
        if (!$this->personnes->contains($personne)) {
            $this->personnes[] = $personne;
            $personne->setIdProfil($this);
        }
        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->personnes->removeElement($personne)) {
            if ($personne->getIdProfil() === $this) {
                $personne->setIdProfil(null);
            }
        }
        return $this;
    }

    public function getPflRang(): ?int
    {
        return $this->pflRang;
    }

    public function setPflRang(?int $pflRang): self  
    {
        $this->pflRang = $pflRang;
        return $this;
    }
    

    public function __toString(): string
    {
        return $this->pflLibelle;
    }
}
