<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Dynamic\TacheIncidents\CreateTachesIncidentController;
use App\Controller\Dynamic\TacheIncidents\CreateTachesIncidentMobileController;
use App\Controller\Dynamic\TacheIncidents\CreateTachesIncidentMobileMultipleController;
use App\Controller\Dynamic\TacheIncidents\DeleteTachesIncidentController;
use App\Controller\Dynamic\TacheIncidents\DeleteTachesIncidentMobileController;
use App\Controller\Dynamic\TacheIncidents\ListeTachesIncidentController;
use App\Controller\Dynamic\TacheIncidents\OneTachesIncidentController;
use App\Controller\Dynamic\TacheIncidents\UpdateTachesIncidentController;
use App\Controller\Dynamic\TacheIncidents\UpdateTachesIncidentMobileController;
use App\Controller\Dynamic\TacheIncidents\UtilsTachesIncidentController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Uid\Uuid;

/**
 * TachesIncidents
 */
#[ORM\Table(name: 'taches_incidents')]
#[ORM\Index(name: 'WDIDX_Taches_Incidents_ID_Personne', columns: ['ID_Personne'])]
#[ORM\Index(name: 'WDIDX_Taches_Incidents_ID_Taches_Planifies', columns: ['ID_Taches_Planifies'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class TachesIncidents
{
    use TimestampableTrait;
    #[ORM\Id]
    #[ORM\Column(name: 'ID_Taches_incidents', unique: true, type: 'string', length: 36, nullable: false)]
     private ?string $id = null;

    #[ORM\Column(name: 'TID_Libelle', type: 'string', length: 50, nullable: false)]
    private $tidLibelle;

    #[ORM\Column(name: 'TID_Commentaire', type: 'text', nullable: false)]
    private $tidCommentaire;

    #[ORM\ManyToOne(targetEntity: 'Personne')]
    #[ORM\JoinColumn(name: 'ID_Personne', referencedColumnName: 'ID_Personne', nullable: true)]
    private ?Personne $idPersonne; //superviseur

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
    private Utilisateur $usercreation;

    #[ORM\ManyToOne(targetEntity: TachesPlanifies::class)]
    #[ORM\JoinColumn(name: 'ID_Taches_Planifies', referencedColumnName: 'ID_Taches_Planifies')]
    private $idTachesPlanifies;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\Column(name: 'DatePointagePlanifies', type: 'datetime', nullable: true)]
    private ?\DateTime $datePointagePlanifies = null;

    //date tacheincidence
    #[ORM\Column(name: 'DateTacheIncidence', type: 'datetime', nullable: true)]
    private ?\DateTime $dateTacheIncidence = null;


    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTidLibelle(): ?string
    {
        return $this->tidLibelle;
    }

    public function setTidLibelle(string $tidLibelle): self
    {
        $this->tidLibelle = $tidLibelle;
        return $this;
    }

    public function getTidCommentaire(): ?string
    {
        return $this->tidCommentaire;
    }

    public function setTidCommentaire(string $tidCommentaire): self
    {
        $this->tidCommentaire = $tidCommentaire;
        return $this;
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

    public function getUsercreation(): ?Utilisateur
    {
        return $this->usercreation;
    }

    public function setUsercreation(?Utilisateur $usercreation): self
    {
        $this->usercreation = $usercreation;
        return $this;
    }

    public function getIdTachesPlanifies(): ?TachesPlanifies
    {
        return $this->idTachesPlanifies;
    }

    public function setIdTachesPlanifies(?TachesPlanifies $idTachesPlanifies): self
    {
        $this->idTachesPlanifies = $idTachesPlanifies;
        return $this;
    }

    public function getDatePointagePlanifies(): ?\DateTime
    {
        return $this->datePointagePlanifies;
    }

    public function setDatePointagePlanifies(?\DateTime $datePointagePlanifies): self
    {
        $this->datePointagePlanifies = $datePointagePlanifies;
        return $this;
    }

    public function getDateTacheIncidence(): ?\DateTime
    {
        return $this->dateTacheIncidence;
    }

    public function setDateTacheIncidence(\DateTime $dateTacheIncidence): self
    {
        $this->dateTacheIncidence = $dateTacheIncidence;
        return $this;
    }


    public function __toString(): string
    {
        return 'Incident de tâche - ' . ($this->tidLibelle ?: 'N/A');
    }
}