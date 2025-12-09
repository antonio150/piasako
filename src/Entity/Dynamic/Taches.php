<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\Dynamic\Personne\ListeElementParametreController;
use App\Controller\Dynamic\Rapport\ListeTachesPlanifiesByTacheController;
use App\Controller\Dynamic\Rapport\RapportParTachesController;
use App\Controller\Dynamic\Rapport\RapportParTachesPlanifierController;
use App\Controller\Dynamic\Rapport\RapportTachePlanifieController;
use App\Controller\Dynamic\Site\ListTimePauseController;
use App\Controller\Dynamic\Site\UpdateSiteDynamicController;
use App\Controller\Dynamic\Tache\CreateTachesController;
use App\Controller\Dynamic\Tache\DeleteTachesController;
use App\Controller\Dynamic\Tache\GetAllActiveTachesController;
use App\Controller\Dynamic\Tache\GetTachesAyantTachePlanifierEncoursController;
use App\Controller\Dynamic\Tache\ListePersonnesByTacheController;
use App\Controller\Dynamic\Tache\ListeTachesAvanceeController;
use App\Controller\Dynamic\Tache\ListeTachesController;
use App\Controller\Dynamic\Tache\ListeTachesMobileController;
use App\Controller\Dynamic\Tache\ListeTravailleurAssignerTachesController;
use App\Controller\Dynamic\Tache\OneTachesController;
use App\Controller\Dynamic\Tache\RapportTacheController;
use App\Controller\Dynamic\Tache\RapportTacheExportController;
use App\Controller\Dynamic\Tache\RemoveTachesController;
use App\Controller\Dynamic\Tache\UpdateTachesController;
use App\Controller\Dynamic\Tache\UtilsTachesController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Uid\Uuid;

/**
 * Taches
 */
#[ORM\Table(name: 'taches')]
#[ORM\Index(name: 'WDIDX_Taches_TAC_Inactif', columns: ['TAC_Inactif'])]
#[ORM\Index(name: 'WDIDX_Taches_TAC_Nom', columns: ['TAC_Nom'])]
#[ORM\Index(name: 'WDIDX_Taches_ID_Taches_Priorites', columns: ['ID_Taches_Priorites'])]
#[ORM\Index(name: 'WDIDX_Taches_ID_Taches_Type', columns: ['ID_Taches_Type'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class Taches
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\Column(name: 'ID_Taches', unique: true, type: 'string', length: 36, nullable: false)]
     private ?string $id = null;

    #[ORM\Column(name: 'TAC_Nom', type: 'string', length: 100, nullable: false)]
    private ?string $tacNom;

    #[ORM\Column(name: 'TAC_Description', type: 'text', nullable: false)]
    private ?string $tacDescription;

    #[ORM\Column(name: 'TAC_NbTravailleurRequis', type: 'integer', nullable: false)]
     private ?int $tacNbtravailleurrequis = 0;

    #[ORM\Column(name: 'TAC_DureeEstimatif', type: 'integer', nullable: false)]
    private ?int $tacDureeestimatif = 0;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $usercreation;

    #[ORM\Column(name: 'TAC_Budget', type: 'decimal', precision: 24, scale: 6, nullable: false, options: ['default' => '0.000000'])]
    private ?float $tacBudget = 0.0;

    #[ORM\Column(name: 'TAC_Inactif', type: 'boolean', nullable: false)]
    private ?bool $tacInactif = false;

    #[ORM\Column(name: 'TAC_DATE_PREVISION', type: 'datetime', nullable: true)]
    private ?\DateTime $datePrevision;

    #[ORM\ManyToOne(targetEntity: 'TachesPriorites')]
    #[ORM\JoinColumn(name: 'ID_Taches_Priorites', referencedColumnName: 'ID_Taches_Priorites')]
    private ?TachesPriorites $idTachesPriorites;

    #[ORM\Column(name: 'TAC_Taux_horaire', type: 'float', nullable: false)]
    private ?float $tacTauxHoraire;

    #[ORM\ManyToOne(targetEntity: 'TachesType')]
    #[ORM\JoinColumn(name: 'ID_Taches_Type', referencedColumnName: 'ID_Taches_Type')]
    private ?TachesType $idTachesType;

    // Add OneToMany relationship
    #[ORM\OneToMany(mappedBy: 'idTaches', targetEntity: TachesPlanifies::class)]
    private Collection $tachesPlanifies;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $periodic = false;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $joursExecution = null; // jour de 0 à 6 (0 = dimanche, 1 = lundi, ..., 6 = samedi)

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $tacShiftJour = true;


    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->tachesPlanifies = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTacNom(): ?string
    {
        return $this->tacNom;
    }

    public function setTacNom(string $tacNom): self
    {
        $this->tacNom = $tacNom;
        return $this;
    }

    public function getTacDescription(): ?string
    {
        return $this->tacDescription;
    }

    public function setTacDescription(string $tacDescription): self
    {
        $this->tacDescription = $tacDescription;
        return $this;
    }

    public function getTacNbtravailleurrequis(): ?int
    {
        return $this->tacNbtravailleurrequis;
    }

    public function setTacNbtravailleurrequis(int $tacNbtravailleurrequis): self
    {
        $this->tacNbtravailleurrequis = $tacNbtravailleurrequis;
        return $this;
    }

    public function getTacDureeestimatif(): ?int
    {
        return $this->tacDureeestimatif;
    }

    public function setTacDureeestimatif(int $tacDureeestimatif): self
    {
        $this->tacDureeestimatif = $tacDureeestimatif;
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

    public function getTacBudget(): ?float
    {
        return $this->tacBudget;
    }

    public function setTacBudget(float $tacBudget): self
    {
        $this->tacBudget = $tacBudget;
        return $this;
    }

    public function isTacInactif(): ?bool
    {
        return $this->tacInactif;
    }

    public function setTacInactif(bool $tacInactif): self
    {
        $this->tacInactif = $tacInactif;
        return $this;
    }

    public function getIdTachesPriorites(): ?TachesPriorites
    {
        return $this->idTachesPriorites;
    }

    public function setIdTachesPriorites(?TachesPriorites $idTachesPriorites): self
    {
        $this->idTachesPriorites = $idTachesPriorites;
        return $this;
    }

    public function getTacTauxHoraire(): ?Float
    {
        return $this->tacTauxHoraire;
    }

    public function setTacTauxHoraire(?Float $tacTauxHoraire): self
    {
        $this->tacTauxHoraire = $tacTauxHoraire;
        return $this;
    }

    public function getIdTachesType(): ?TachesType
    {
        return $this->idTachesType;
    }

    public function setIdTachesType(?TachesType $idTachesType): self
    {
        $this->idTachesType = $idTachesType;
        return $this;
    }

    // Add getters and setters for tachesPlanifies
    public function getTachesPlanifies(): Collection
    {
        return $this->tachesPlanifies;
    }

    public function addTachesPlanifie(TachesPlanifies $tachesPlanifie): self
    {
        if (!$this->tachesPlanifies->contains($tachesPlanifie)) {
            $this->tachesPlanifies[] = $tachesPlanifie;
            $tachesPlanifie->setIdTaches($this);
        }
        return $this;
    }

    public function removeTachesPlanifie(TachesPlanifies $tachesPlanifie): self
    {
        if ($this->tachesPlanifies->removeElement($tachesPlanifie)) {
            if ($tachesPlanifie->getIdTaches() === $this) {
                $tachesPlanifie->setIdTaches(null);
            }
        }
        return $this;
    }

    public function getDatePrevision():?\DateTime
    {
        return $this->datePrevision;
    }

    public function setDatePrevision(?\DateTime $datePrevision):self
    {
        $this->datePrevision = $datePrevision;
        return $this;
    }

    public function getJoursExecution(): ?array
    {
        return $this->joursExecution;
    }

    public function setJoursExecution(?array $joursExecution): self
    {
        $this->joursExecution = $joursExecution;
        return $this;
    }
    public function isPeriodic(): bool
    {
        return $this->periodic;
    }
    public function setPeriodic(bool $periodic): self
    {
        $this->periodic = $periodic;
        return $this;
    }
    
    public function doitEtreExecuteAujourdHui(): bool
    {
        $aujourdHui = (int) date('w'); // 0 = dimanche, 6 = samedi
        return is_array($this->joursExecution) && in_array($aujourdHui, $this->joursExecution);
    }

    public function isTacShiftJour(): ?bool
    {
        return $this->tacShiftJour;
    }

    public function setTacShiftJour(bool $tacShiftJour): static
    {
        $this->tacShiftJour = $tacShiftJour;

        return $this;
    }


    public function __toString(): string
    {
        return $this->tacNom;
    }
}
