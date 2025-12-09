<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\Dynamic\TachePriorites\CreateTachesPrioritesController;
use App\Controller\Dynamic\TachePriorites\DeleteTachePrioritesController;
use App\Controller\Dynamic\TachePriorites\ListeTachePrioriteController;
use App\Controller\Dynamic\TachePriorites\OneTachePrioriteController;
use App\Controller\Dynamic\TachePriorites\UpdateTachePrioritesController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * TachesPriorites
 */
#[ORM\Table(name: 'taches_priorites')]
#[ORM\Index(name: 'WDIDX_Taches_Priorites_TPT_Libelle', columns: ['TPT_Libelle'])]
#[ORM\Index(name: 'WDIDX_Taches_Priorites_TPT_Niveau', columns: ['TPT_Niveau'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class TachesPriorites
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\Column(name: 'ID_Taches_Priorites', unique: true, type: 'string', length: 36, nullable: false)]
    private ?string $id = null;

    #[ORM\Column(name: 'TPT_Libelle', type: 'string', length: 50, nullable: false)]
    private string $tptLibelle;

    #[ORM\Column(name: 'TPT_Niveau', type: 'integer', nullable: false)]
    private int $tptNiveau = 0;

    #[ORM\Column(name: 'TPT_Couleur', type: 'string', length: 7, nullable: false)]
    private string $tptCouleur;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
    private Utilisateur $usercreation;

    // Add OneToMany relationship
    #[ORM\OneToMany(mappedBy: 'idTachesPriorites', targetEntity: Taches::class)]
    private Collection $taches;

    
    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\Column(name: 'TPT_Slug', type: 'string', length: 50, nullable: true)]
    private string $tptSlug;


    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->taches = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTptLibelle(): string
    {
        return $this->tptLibelle;
    }

    public function setTptLibelle(string $tptLibelle): self
    {
        $this->tptLibelle = $tptLibelle;
        return $this;
    }

    public function getTptNiveau(): int
    {
        return $this->tptNiveau;
    }

    public function setTptNiveau(int $tptNiveau): self
    {
        $this->tptNiveau = $tptNiveau;
        return $this;
    }

    public function getTptCouleur(): string
    {
        return $this->tptCouleur;
    }

    public function setTptCouleur(string $tptCouleur): self
    {
        $this->tptCouleur = $tptCouleur;
        return $this;
    }

    public function getUsercreation(): Utilisateur
    {
        return $this->usercreation;
    }

    public function setUsercreation(?Utilisateur $usercreation): self
    {
        $this->usercreation = $usercreation;
        return $this;
    }

    // Add getters and setters for taches
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Taches $tache): self
    {
        if (!$this->taches->contains($tache)) {
            $this->taches[] = $tache;
            $tache->setIdTachesPriorites($this);
        }
        return $this;
    }

    public function removeTache(Taches $tache): self
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getIdTachesPriorites() === $this) {
                $tache->setIdTachesPriorites(null);
            }
        }
        return $this;
    }

    public function getTptSlug(): string
    {
        return $this->tptSlug;
    }

    public function setTptSlug(?string $tptSlug): self
    {
        $this->tptSlug = $tptSlug;
        return $this;
    }

    public function __toString(): string
    {
        return 'Priorité : ' . $this->tptLibelle . ' (Niveau ' . $this->tptNiveau . ')';
    }


}
