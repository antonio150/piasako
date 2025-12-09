<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Dynamic\PointagePlanification\ExportPointagePlanificationController;
use App\Controller\Dynamic\Rapport\ExportTacheplanifieController;
use App\Controller\Dynamic\TachePlanifies\ChangeAvalideeToEnAttenteTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\CreateTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\CreateTachesPlanifiesMobileController;
use App\Controller\Dynamic\TachePlanifies\DeleteTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\EditEstPauseTachePlanifieMobile;
use App\Controller\Dynamic\TachePlanifies\EquipeByTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\GetHistoriquePauseTachePlanifieController;
use App\Controller\Dynamic\TachePlanifies\ListePersonneNonTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\ListePersonneTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\ListeTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\ListeTachePlanifieMobileController;
use App\Controller\Dynamic\TachePlanifies\ListeTachesPlanifiesByTacheController;
use App\Controller\Dynamic\TachePlanifies\ListeTachesPlanifiesEncoursController;
use App\Controller\Dynamic\TachePlanifies\ListeTachesPlanifiesEncoursOptimiserController;
use App\Controller\Dynamic\TachePlanifies\ListeTachesPlanifiesGanttController;
use App\Controller\Dynamic\TachePlanifies\ListeTachesPlanifiesParPersonneController;
use App\Controller\Dynamic\TachePlanifies\OneTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\UpdateDateTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\UpdateStatutAnnulerTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\UpdateStatutTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\UpdateStatutTachesPlanifiesMobileController;
use App\Controller\Dynamic\TachePlanifies\UpdateTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\UtilsTachesPlanifiesController;
use App\Controller\Dynamic\TachePlanifies\GetTachesStatutHistoriqueByPlanifieController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use App\Entity\Dynamic\Taches;
use App\Entity\Dynamic\Personne;
use App\Entity\Dynamic\Utilisateur;
use App\Entity\Dynamic\Parcelle;
use App\Entity\Dynamic\TachesTravailleur;
use App\Entity\Dynamic\TachesIncidents;
use App\Entity\Dynamic\TachesHistoriques;
use App\Entity\Dynamic\TachesStatut;
use App\Enum\DurationUnit;
use App\Enum\ShiftType;
use App\Enum\TplOptions;
use Doctrine\DBAL\Types\Types;

/**
 * TachesPlanifies
 */
#[ORM\Table(name: 'taches_planifies')]
#[ORM\Index(name: 'WDIDX_Taches_Planifies_ID_Personne', columns: ['ID_Personne'])]
#[ORM\Index(name: 'WDIDX_Taches_Planifies_ID_Taches', columns: ['ID_Taches'])]
#[ORM\Index(name: 'WDIDX_Taches_Planifies_ID_Parcelle', columns: ['ID_Parcelle'])]
// #[ORM\Index(name: 'WDIDX_Taches_Planifies_ID_Admin', columns: ['ID_Admin'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class TachesPlanifies
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'ID_Taches_Planifies', unique: true, type: 'string', length: 36, nullable: false)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: 'Personne')]
    #[ORM\JoinColumn(name: 'ID_Personne', referencedColumnName: 'ID_Personne', nullable: true)]
    private ?Personne $idPersonne;

    #[ORM\Column(name: 'TPL_DatePlanification', type: 'datetime', nullable: true)]
    private ?\DateTime $tplDateplanification;

    #[ORM\Column(name: 'TPL_DateFinPlanification', type: 'datetime', nullable: true)]
    private ?\DateTime $tplDatefinplanification;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $usercreation;

    #[ORM\ManyToOne(targetEntity: 'Taches')]
    #[ORM\JoinColumn(name: 'ID_Taches', referencedColumnName: 'ID_Taches')]
    private ?Taches $idTaches;

    #[ORM\ManyToOne(targetEntity: 'Parcelle')]
    #[ORM\JoinColumn(name: 'ID_Parcelle', referencedColumnName: 'ID_Parcelle')]
    private ?Parcelle $idParcelle;

    #[ORM\OneToMany(mappedBy: 'idTachesPlanifies', targetEntity: TachesTravailleur::class)]
    private Collection $tachesTravailleurs;

    #[ORM\ManyToMany(targetEntity: Utilisateur::class)]
    #[ORM\JoinTable(name: 'taches_planifies_utilisateur')]
    #[ORM\JoinColumn(name: 'ID_Taches_Planifies', referencedColumnName: 'ID_Taches_Planifies', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'ID_User_Site', referencedColumnName: 'ID_User_Site', nullable: false)]
    private Collection $admins;


    #[ORM\OneToMany(mappedBy: 'idTachesPlanifies', targetEntity: TachesIncidents::class)]
    private Collection $tachesIncidents;

    #[ORM\OneToMany(mappedBy: 'idTachesPlanifies', targetEntity: TachesHistoriques::class)]
    private Collection $tachesHistoriques;

    #[ORM\OneToMany(mappedBy: 'tachePlanifie', targetEntity: TachesStatutHistorique::class)]
    private Collection $tachesStatutHistoriques;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\Column(name: 'TPL_Cloturer', type: 'boolean', nullable: false)]
    private bool $tplCloturer = false;

    #[ORM\Column(name: 'TPL_Pause', type: 'boolean', nullable: false)]
    private bool $tplPause = false;

    #[ORM\ManyToOne(targetEntity: TachesStatut::class)]
    #[ORM\JoinColumn(name: 'ID_Taches_Statut', referencedColumnName: 'ID_Taches_Statut', nullable: false)]
    private ?TachesStatut $idTachesStatut = null;

    #[ORM\OneToMany(mappedBy: 'tachesPlanifies', targetEntity: PointagePlanification::class)]
    private Collection $pointages;

    //attribut pour stocker la date de debut et la duree de la journee, il faut le stocker pendant la creation de la planification
    #[ORM\Column(name: 'TPL_StartDate', type: 'datetime', nullable: true)]
    private ?\DateTime $startDate = null;

    //stockage de duree d'une journee
    #[ORM\Column(name: 'TPL_Duration_Day', type: 'integer', nullable: true)]
    private ?int $durationDay = null;


    #[ORM\Column(name: 'TPL_Duration_Value', type: 'integer', nullable: true)]
    private ?int $durationValue = null;

    #[ORM\Column(name: 'TPL_Duration_Unit', type: 'string', length: 20, nullable: true, enumType: DurationUnit::class)]
    private ?DurationUnit $durationUnit = null;

    #[ORM\OneToMany(mappedBy: 'tachePlanifie', targetEntity: CommentaireJustificatif::class, cascade: ['remove'])]
    // #[Groups(['tachesplanifies:read', 'commentairejustificatif:read'])]
    private Collection $commentairesJustificatifs;

    #[ORM\Column(name: 'TPL_EffectifTravailleurRequis', type: 'integer', nullable: true)]
    private ?int $effectifTravailleurRequis = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $enRetard = false;

    #[ORM\Column(name: 'TPL_Shift', type: 'string', length: 20, nullable: true, enumType: ShiftType::class)]
    private ?ShiftType $shift = null;

    #[ORM\OneToMany(mappedBy: 'tachePlanifie', targetEntity: HistoriquePauseTachePlanifie::class)]
    private Collection $historiquePauseTachePlanifie;

    #[ORM\Column(name: 'TPL_Options', type: 'string', length: 20, nullable: true, enumType: TplOptions::class)]
    private ?TplOptions $tplOptions = null;
    

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->tachesTravailleurs = new ArrayCollection();
        $this->tachesIncidents = new ArrayCollection();
        $this->tachesHistoriques = new ArrayCollection();
        $this->tachesStatutHistoriques = new ArrayCollection();
        $this->commentairesJustificatifs = new ArrayCollection();
        $this->pointages = new ArrayCollection();
        $this->historiquePauseTachePlanifie = new ArrayCollection();
        $this->admins = new ArrayCollection();
        $this->enRetard = false;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getIdPersonne(): ?Personne
    {
        return $this->idPersonne;
    }

    public function setIdPersonne(?Personne $idPersonne): self
    {
        $this->idPersonne = $idPersonne;
        return $this;
    }

    public function getTplDateplanification(): ?\DateTime
    {
        return $this->tplDateplanification;
    }

    public function setTplDateplanification(?\DateTime $tplDateplanification): self
    {
        $this->tplDateplanification = $tplDateplanification;
        return $this;
    }

    public function getTplDatefinplanification(): ?\DateTime
    {
        return $this->tplDatefinplanification;
    }

    public function setTplDatefinplanification(?\DateTime $tplDatefinplanification): self
    {
        $this->tplDatefinplanification = $tplDatefinplanification;
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

    public function getIdTaches(): ?Taches
    {
        return $this->idTaches;
    }

    public function setIdTaches(?Taches $idTaches): self
    {
        $this->idTaches = $idTaches;
        return $this;
    }

    public function getIdParcelle(): ?Parcelle
    {
        return $this->idParcelle;
    }

    public function setIdParcelle(?Parcelle $idParcelle): self
    {
        $this->idParcelle = $idParcelle;
        return $this;
    }

    public function getTachesTravailleurs(): Collection
    {
        return $this->tachesTravailleurs;
    }

    public function addTachesTravailleur(TachesTravailleur $tachesTravailleur): self
    {
        if (!$this->tachesTravailleurs->contains($tachesTravailleur)) {
            $this->tachesTravailleurs[] = $tachesTravailleur;
            $tachesTravailleur->setIdTachesPlanifies($this);
        }
        return $this;
    }

    public function removeTachesTravailleur(TachesTravailleur $tachesTravailleur): self
    {
        if ($this->tachesTravailleurs->removeElement($tachesTravailleur)) {
            if ($tachesTravailleur->getIdTachesPlanifies() === $this) {
                $tachesTravailleur->setIdTachesPlanifies(null);
            }
        }
        return $this;
    }

    public function getTachesIncidents(): Collection
    {
        return $this->tachesIncidents;
    }

    public function addTachesIncident(TachesIncidents $tachesIncident): self
    {
        if (!$this->tachesIncidents->contains($tachesIncident)) {
            $this->tachesIncidents[] = $tachesIncident;
            $tachesIncident->setIdTachesPlanifies($this);
        }
        return $this;
    }

    public function removeTachesIncident(TachesIncidents $tachesIncident): self
    {
        if ($this->tachesIncidents->removeElement($tachesIncident)) {
            if ($tachesIncident->getIdTachesPlanifies() === $this) {
                $tachesIncident->setIdTachesPlanifies(null);
            }
        }
        return $this;
    }

    public function getTachesHistoriques(): Collection
    {
        return $this->tachesHistoriques;
    }

    public function addTachesHistorique(TachesHistoriques $tachesHistorique): self
    {
        if (!$this->tachesHistoriques->contains($tachesHistorique)) {
            $this->tachesHistoriques[] = $tachesHistorique;
            $tachesHistorique->setIdTachesPlanifies($this);
        }
        return $this;
    }

    public function removeTachesHistorique(TachesHistoriques $tachesHistorique): self
    {
        if ($this->tachesHistoriques->removeElement($tachesHistorique)) {
            if ($tachesHistorique->getIdTachesPlanifies() === $this) {
                $tachesHistorique->setIdTachesPlanifies(null);
            }
        }
        return $this;
    }

    public function getTachesStatutHistorique(): Collection
    {
        return $this->tachesStatutHistoriques;
    }

    public function addTacheStatutHistorique(TachesStatutHistorique $tachesStatutHistoriques): self
    {
        if (!$this->tachesStatutHistoriques->contains($tachesStatutHistoriques)) {
            $this->tachesStatutHistoriques[] = $tachesStatutHistoriques;
            $tachesStatutHistoriques->setTachePlanifie($this);
        }
        return $this;
    }

    public function removeTacheStatutHistorique(TachesStatutHistorique $tachesStatutHistoriques): self
    {
        if ($this->tachesStatutHistoriques->removeElement($tachesStatutHistoriques)) {
            if ($tachesStatutHistoriques->getTacheStatut() === $this) {
                $tachesStatutHistoriques->setTachePlanifie(null);
            }
        }
        return $this;
    }

    public function getTplCloturer(): bool
    {
        return $this->tplCloturer;
    }

    public function setTplCloturer(bool $tplCloturer): self
    {
        $this->tplCloturer = $tplCloturer;
        return $this;
    }

    public function getTplPause(): bool
    {
        return $this->tplPause;
    }

    public function setTplPause(bool $tplPause): self
    {
        $this->tplPause = $tplPause;
        return $this;
    }

    public function getIdTachesStatut(): ?TachesStatut
    {
        return $this->idTachesStatut;
    }

    public function setIdTachesStatut(?TachesStatut $idTachesStatut): self
    {
        $this->idTachesStatut = $idTachesStatut;
        return $this;
    }

    public function getPointages(): Collection
    {
        return $this->pointages;
    }

    public function addPointage(PointagePlanification $pointage): self
    {
        if (!$this->pointages->contains($pointage)) {
            $this->pointages[] = $pointage;
            $pointage->setTachesPlanifies($this);
        }
        return $this;
    }

    public function removePointage(PointagePlanification $pointage): self
    {
        if ($this->pointages->removeElement($pointage)) {
            if ($pointage->getTachesPlanifies() === $this) {
                $pointage->setTachesPlanifies(null);
            }
        }
        return $this;
    }

    public function getDurationValue(): ?int
    {
        return $this->durationValue;
    }

    public function setDurationValue(?int $durationValue): self
    {
        $this->durationValue = $durationValue;
        return $this;
    }

    public function getDurationUnit(): ?DurationUnit
    {
        return $this->durationUnit;
    }

    public function setDurationUnit(?DurationUnit $durationUnit): self
    {
        $this->durationUnit = $durationUnit;
        return $this;
    }

    public function getDurationDay(): ?int
    {
        return $this->durationDay;
    }

    public function setDurationDay(?int $durationDay): self
    {
        $this->durationDay = $durationDay;
        return $this;
    }

    //recuperation de startdate
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }
    public function setStartDate(\DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Retourne la durée sous forme de chaîne (ex: "5 jours")
     */
    public function getDurationAsString(): string
    {
        if ($this->durationValue === null || $this->durationUnit === null) {
            return 'N/A';
        }
        return sprintf('%d %s', $this->durationValue, $this->durationUnit->value);
    }

    /**
     * @return Collection|Utilisateur[]
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function addAdmin(Utilisateur $admin): self
    {
        if (!$this->admins->contains($admin)) {
            $this->admins[] = $admin;
        }

        return $this;
    }

    public function removeAdmin(Utilisateur $admin): self
    {
        $this->admins->removeElement($admin);

        return $this;
    }



    public function getCommentairesJustificatifs(): Collection
    {
        return $this->commentairesJustificatifs;
    }

    public function addCommentaireJustificatif(CommentaireJustificatif $commentaireJustificatif): self
    {
        if (!$this->commentairesJustificatifs->contains($commentaireJustificatif)) {
            $this->commentairesJustificatifs[] = $commentaireJustificatif;
            $commentaireJustificatif->setTachePlanifie($this);
        }
        return $this;
    }

    public function removeCommentaireJustificatif(CommentaireJustificatif $commentaireJustificatif): self
    {
        if ($this->commentairesJustificatifs->removeElement($commentaireJustificatif)) {
            if ($commentaireJustificatif->getTachePlanifie() === $this) {
                $commentaireJustificatif->setTachePlanifie(null);
            }
        }
        return $this;
    }

    public function getEffectifTravailleurRequis(): ?int
    {
        return $this->effectifTravailleurRequis;
    }

    public function setEffectifTravailleurRequis(?int $effectifTravailleurRequis): self
    {
        $this->effectifTravailleurRequis = $effectifTravailleurRequis;
        return $this;
    }

    public function isEnRetard(): bool
    {
        return $this->enRetard;
    }

    public function setEnRetard(bool $enRetard): self
    {
        $this->enRetard = $enRetard;
        return $this;
    }

    public function getShift(): ?ShiftType
    {
        return $this->shift;
    }

    public function setShift(?ShiftType $shift): self
    {
        $this->shift = $shift;
        return $this;
    }

    public function getHistoriquePauseTachePlanifies(): Collection
    {
        return $this->historiquePauseTachePlanifie;
    }

    public function addHistoriquePauseTachePlanifies(HistoriquePauseTachePlanifie $historiquePauseTachePlanifie): self
    {
        if (!$this->historiquePauseTachePlanifie->contains($historiquePauseTachePlanifie)) {
            $this->historiquePauseTachePlanifie[] = $historiquePauseTachePlanifie;
            $historiquePauseTachePlanifie->setTachePlanifie($this);
        }
        return $this;
    }

    public function removeHistoriquePauseTachePlanifies(HistoriquePauseTachePlanifie $historiquePauseTachePlanifie): self
    {
        if ($this->historiquePauseTachePlanifie->removeElement($historiquePauseTachePlanifie)) {
            if ($historiquePauseTachePlanifie->getTachePlanifie() === $this) {
                $historiquePauseTachePlanifie->getTachePlanifie(null);
            }
        }
        return $this;
    }

    public function getTplOptions(): ?TplOptions
    {
        return $this->tplOptions;
    }

    public function setTplOptions(?TplOptions $tplOptions): self
    {
        $this->tplOptions = $tplOptions;
        return $this;
    }


    public function __toString(): string
    {
        return 'Tâche planifiée - ' . ($this->idTaches ? $this->idTaches->getTacNom() : 'N/A');
    }
}