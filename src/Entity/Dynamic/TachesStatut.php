<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\Dynamic\Rapport\RapportTachesStatusController;
use App\Controller\Dynamic\TacheStatus\CreateTachesStatusController;
use App\Controller\Dynamic\TacheStatus\DeleteTachesStatusController;
use App\Controller\Dynamic\TacheStatus\ListeTachesStatusByPlanificationController;
use App\Controller\Dynamic\TacheStatus\ListeTachesStatusController;
use App\Controller\Dynamic\TacheStatus\ListeTachesStatusSansPaginateController;
use App\Controller\Dynamic\TacheStatus\ListeTachesStatusSansPaginateEncoursTerminController;
use App\Controller\Dynamic\TacheStatus\OneTachesStatusController;
use App\Controller\Dynamic\TacheStatus\UpdateTachesStatusController;
use App\Entity\Dynamic\Utilisateur;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * TachesStatut
 */
#[ORM\Table(name: 'taches_statut')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class TachesStatut
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'ID_Taches_Statut', unique: true, type: 'string', length: 36, nullable: false)]
    private ?string $id = null;

    #[ORM\Column(name: 'TST_Libelle', type: 'string', length: 50, nullable: false)]
    private string $tstLibelle;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $usercreation;

    #[ORM\Column(name: 'TST_Couleur', type: 'string', length: 7, nullable: false)]
    private string $tstCouleur;

  
    #[ORM\Column(name: 'TST_Slug', type: 'string', length: 50, nullable: false)]
    private ?string $tstSlug;

    #[ORM\Column(name: 'TST_Rang', type: 'integer', length: 50, nullable: true)]
    private ?int $tstRang;

    #[ORM\OneToMany(mappedBy: 'idTachesStatut', targetEntity: TachesPlanifies::class)]
    private Collection $tachesPlanifies;

    #[ORM\OneToMany(mappedBy: 'tacheStatut', targetEntity: TachesStatutHistorique::class)]
    private Collection $tachesStatutHistorique;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->tachesPlanifies = new ArrayCollection();
        $this->tachesStatutHistorique = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTstLibelle(): string
    {
        return $this->tstLibelle;
    }

    public function setTstLibelle(string $tstLibelle): self
    {
        $this->tstLibelle = $tstLibelle;
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

    public function getTstCouleur(): string
    {
        return $this->tstCouleur;
    }

    public function setTstCouleur(string $tstCouleur): self
    {
        $this->tstCouleur = $tstCouleur;
        return $this;
    }

    public function getTstSlug(): ?string
    {
        return $this->tstSlug;
    }

    public function setTstSlug(string $tstSlug): self
    {
        $this->tstSlug = $tstSlug;
        return $this;
    }

    public function getTstRang(): ?int
    {
        return $this->tstRang;
    }

    public function setTstRang(int $tstRang): self
    {
        $this->tstRang = $tstRang;
        return $this;
    }

    public function getTachesPlanifies(): Collection
    {
        return $this->tachesPlanifies;
    }

    public function addTachePlanifie(TachesPlanifies $tachePlanifie): self
    {
        if (!$this->tachesPlanifies->contains($tachePlanifie)) {
            $this->tachesPlanifies[] = $tachePlanifie;
            $tachePlanifie->setIdTachesStatut($this);
        }
        return $this;
    }

    public function removeTachePlanifie(TachesPlanifies $tachePlanifie): self
    {
        if ($this->tachesPlanifies->removeElement($tachePlanifie)) {
            if ($tachePlanifie->getIdTachesStatut() === $this) {
                $tachePlanifie->setIdTachesStatut(null);
            }
        }
        return $this;
    }

    public function getTachesStatutHistorique(): Collection
    {
        return $this->tachesStatutHistorique;
    }

    public function addTacheStatutHistorique(TachesStatutHistorique $tacheStatutHistorique): self
    {
        if (!$this->tachesStatutHistorique->contains($tacheStatutHistorique)) {
            $this->tachesStatutHistorique[] = $tacheStatutHistorique;
            $tacheStatutHistorique->setTacheStatut($this);
        }
        return $this;
    }

    public function removeTacheStatutHistorique(TachesStatutHistorique $tacheStatutHistorique): self
    {
        if ($this->tachesStatutHistorique->removeElement($tacheStatutHistorique)) {
            if ($tacheStatutHistorique->getTacheStatut() === $this) {
                $tacheStatutHistorique->setTacheStatut(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return 'Statut : ' . $this->tstLibelle;
    }
}