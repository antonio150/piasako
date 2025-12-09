<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Controller\Dynamic\TachePlanifies\UpdatePauseTachesPlanifiesController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use App\Entity\Dynamic\TachesPlanifies;

/**
 * HistoriquePauseTachePlanifie
 */
#[ORM\Table(name: 'historique_pause_tache_planifie')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class HistoriquePauseTachePlanifie
{
    #[ORM\Id]
    #[ORM\Column(name: 'ID_Tache_Related', unique: true, type: 'string', length: 36, nullable: false)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: TachesPlanifies::class)]
    #[ORM\JoinColumn(name: 'ID_Taches_Planifies', referencedColumnName: 'ID_Taches_Planifies', nullable: false)]
    private ?TachesPlanifies $tachePlanifie = null;

    #[ORM\Column(name: 'DateDebut', type: 'datetime', nullable: true)]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column(name: 'DateFin', type: 'datetime', nullable: true)]
    private ?\DateTime $dateFin = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: 'Personne')]
    #[ORM\JoinColumn(name: 'ID_Personne', referencedColumnName: 'ID_Personne', nullable: true)]
    private ?Personne $personne;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTime $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTime $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(?Personne $personne): void
    {
        $this->personne = $personne;
    }

    public function __toString(): string
    {
        return 'Tache Related - ' . ($this->id ?: 'N/A');
    }
}