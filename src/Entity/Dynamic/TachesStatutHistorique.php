<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\Dynamic\TacheStatus\CreateTachesStatusController;
use App\Controller\Dynamic\TacheStatus\DeleteTachesStatusController;
use App\Controller\Dynamic\TacheStatus\ListeTachesStatusController;
use App\Controller\Dynamic\TacheStatus\ListeTachesStatusSansPaginateController;
use App\Controller\Dynamic\TacheStatus\OneTachesStatusController;
use App\Controller\Dynamic\TacheStatus\UpdateTachesStatusController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * TachesStatutHistorique
 */
#[ORM\Table(name: 'taches_statut_historique')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class TachesStatutHistorique
{
    

    #[ORM\Id]
    #[ORM\Column(name: 'ID_Taches_Statut_Historique', unique: true, type: 'string', length: 36, nullable: false)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: TachesStatut::class, inversedBy: 'tachesStatutHistorique')]
    #[ORM\JoinColumn(name: 'taches_statut', referencedColumnName: 'ID_Taches_Statut')]
    private ?TachesStatut $tacheStatut = null;

    #[ORM\ManyToOne(targetEntity: TachesPlanifies::class, inversedBy: 'tachesStatutHistoriques')]
    #[ORM\JoinColumn(name: 'taches_planifie', referencedColumnName: 'ID_Taches_Planifies')]
    private ?TachesPlanifies $tachePlanifie = null;

    #[ORM\ManyToOne(targetEntity: 'Personne')]
    #[ORM\JoinColumn(name: 'ID_Personne', referencedColumnName: 'ID_Personne', nullable: true)]
    private ?Personne $idPersonne;

    #[ORM\Column(name: 'TPL_DatePlanification', type: 'datetime', nullable: true)]
    private ?\DateTime $tplDateplanification;
  
    #[ORM\Column(name: 'TPL_DateFinPlanification', type: 'datetime', nullable: true)]
    private ?\DateTime $tplDatefinplanification;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(name: 'TPL_DateModification', type: 'datetime', nullable: true)]
    private ?\DateTime $tplDateModfication = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTacheStatut(): ?TachesStatut
    {
        return $this->tacheStatut;
    }

    public function setTacheStatut(?TachesStatut $tacheStatut): self
    {
        $this->tacheStatut = $tacheStatut;
        return $this;
    }

    public function getTachePlanifie(): ?TachesPlanifies
    {
        return $this->tachePlanifie;
    }

    public function setTachePlanifie(?TachesPlanifies $tachePlanifie): self
    {
        $this->tachePlanifie = $tachePlanifie;
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

     public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getIdPersonne(): ?Personne
    {
        return $this->idPersonne;
    }

    public function setIdPersonne(?Personne $idPersonne): void
    {
        $this->idPersonne = $idPersonne;
    }

    public function getTplDateModfication(): ?\DateTimeInterface
    {
        return $this->tplDateModfication;
    }

    public function setTplDateModfication(?\DateTimeInterface $tplDateModfication): self
    {
        $this->tplDateModfication = $tplDateModfication;
        return $this;
    }
}