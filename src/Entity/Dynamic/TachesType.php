<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Dynamic\Equipe\CreateEquipeController;
use App\Controller\Dynamic\TacheType\CreateTachesTypeController;
use App\Controller\Dynamic\TacheType\DeleteTacheTypeController;
use App\Controller\Dynamic\TacheType\ListeTacheTypeController;
use App\Controller\Dynamic\TacheType\OneTacheTypeController;
use App\Controller\Dynamic\TacheType\UpdateTacheTypeController;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * TachesType
 */
#[ORM\Table(name: 'taches_type')]
#[ORM\Index(name: 'WDIDX_Taches_Type_TPT_Planifiable', columns: ['TPT_Planifiable'])]
#[ORM\Index(name: 'WDIDX_Taches_Type_TTP_Libelle', columns: ['TTP_Libelle'])]
#[ORM\Index(name: 'WDIDX_Taches_Type_TPT_Comptabiliser', columns: ['TPT_Comptabiliser'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class TachesType
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\Column(name: 'ID_Taches_Type', unique: true, type: 'string', length: 36, nullable: false)]
    private ?string $id = null;

    #[ORM\Column(name: 'TTP_Libelle', type: 'string', length: 50, nullable: false)]
    private string $ttpLibelle;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site',  nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $usercreation;

    #[ORM\Column(name: 'TTP_Couleur', type: 'string', length: 6, nullable: false)]
    private string $ttpCouleur;

    #[ORM\Column(name: 'TPT_Comptabiliser', type: 'boolean', nullable: false)]
    private bool $tptComptabiliser = false;

    #[ORM\Column(name: 'TPT_Planifiable', type: 'boolean', nullable: false)]
    private bool $tptPlanifiable = false;

    // Add OneToMany relationship
    #[ORM\OneToMany(mappedBy: 'idTachesType', targetEntity: Taches::class)]
    private Collection $taches;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    //entite pour le nombre de travailleurs requis
    #[ORM\Column(name: 'TPT_Nb_Travailleur_Requis', type: 'integer', nullable: true)]
    private ?int $tptNbTravailleurRequis = 0;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->taches = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTtpLibelle(): string
    {
        return $this->ttpLibelle;
    }

    public function setTtpLibelle(string $ttpLibelle): self
    {
        $this->ttpLibelle = $ttpLibelle;
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

    public function getTtpCouleur(): string
    {
        return $this->ttpCouleur;
    }

    public function setTtpCouleur(string $ttpCouleur): self
    {
        $this->ttpCouleur = $ttpCouleur;
        return $this;
    }

    public function isTptComptabiliser(): bool
    {
        return $this->tptComptabiliser;
    }

    public function setTptComptabiliser(bool $tptComptabiliser): self
    {
        $this->tptComptabiliser = $tptComptabiliser;
        return $this;
    }

    public function isTptPlanifiable(): bool
    {
        return $this->tptPlanifiable;
    }

    public function setTptPlanifiable(bool $tptPlanifiable): self
    {
        $this->tptPlanifiable = $tptPlanifiable;
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
            $tache->setIdTachesType($this);
        }
        return $this;
    }

    public function removeTache(Taches $tache): self
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getIdTachesType() === $this) {
                $tache->setIdTachesType(null);
            }
        }
        return $this;
    }

    public function getTptNbTravailleurRequis(): ?int
    {
        return $this->tptNbTravailleurRequis;
    }

    public function setTptNbTravailleurRequis(?int $tptNbTravailleurRequis): self
    {
        $this->tptNbTravailleurRequis = $tptNbTravailleurRequis;
        return $this;
    }

    public function __toString(): string
    {
        return $this->ttpLibelle;
    }
}
