<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\Dynamic\Pointage\CreatePointageController;
use App\Controller\Dynamic\Pointage\CreatePointageControllerMobile;
use App\Controller\Dynamic\Pointage\CreatePointageEntreeControllerMobile;
use App\Controller\Dynamic\Pointage\CreatePointageSortieControllerMobile;
use App\Controller\Dynamic\Pointage\CreatePointageVerificationControllerMobile;
use App\Controller\Dynamic\Pointage\DeletePointageController;
use App\Controller\Dynamic\Pointage\ExportPointageController;
use App\Controller\Dynamic\Pointage\ExportPointageTravailleurController;
use App\Controller\Dynamic\Pointage\ListePointageController;
use App\Controller\Dynamic\Pointage\ListePointageSansPaginateController;
use App\Controller\Dynamic\Pointage\UpdatePointageController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Uid\Uuid;

/**
 * Pointage
 */
#[ORM\Table(name: 'pointage')]
#[ORM\Index(name: 'WDIDX_Pointage_ID_Parcelle', columns: ['ID_Parcelle'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class Pointage
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\Column(name: 'ID_Pointage', type: 'string', length: 36, unique: true, nullable: true)]
    private ?string $idPointage = null;

    #[ORM\Column(name: 'PTG_DateDebut', type: 'date', nullable: true)]
    private ?\DateTimeInterface $ptgDatedebut = null;

    #[ORM\Column(name: 'PTG_HeureDebut', type: 'time', nullable: true)]
    private ?\DateTimeInterface $ptgHeuredebut = null;

    #[ORM\Column(name: 'PTG_DateFin', type: 'date', nullable: true)]
    private ?\DateTimeInterface $ptgDatefin = null;

    #[ORM\Column(name: 'PTG_HeureFin', type: 'time', nullable: true)]
    private ?\DateTimeInterface $ptgHeurefin = null;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $usercreation = null;

    #[ORM\ManyToOne(targetEntity: Parcelle::class)]
    #[ORM\JoinColumn(name: 'ID_Parcelle', referencedColumnName: 'ID_Parcelle', nullable: true)]
    private ?Parcelle $idParcelle = null;

    #[ORM\ManyToOne(targetEntity: Personne::class)]
    #[ORM\JoinColumn(name: 'ID_Personne', referencedColumnName: 'ID_Personne', nullable: true, onDelete: 'CASCADE')]
    private ?Personne $idPersonne = null;

    public function __construct()
    {
        $this->idPointage = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }
    public function getIdPointage(): ?string
    {
        return $this->idPointage;
    }

    public function getPtgDatedebut(): ?\DateTimeInterface
    {
        return $this->ptgDatedebut;
    }

    public function setPtgDatedebut(?\DateTimeInterface $ptgDatedebut): self
    {
        $this->ptgDatedebut = $ptgDatedebut;
        return $this;
    }

    public function getPtgHeuredebut(): ?\DateTimeInterface
    {
        return $this->ptgHeuredebut;
    }

    public function setPtgHeuredebut(?\DateTimeInterface $ptgHeuredebut): self
    {
        $this->ptgHeuredebut = $ptgHeuredebut;
        return $this;
    }

    public function getPtgDatefin(): ?\DateTimeInterface
    {
        return $this->ptgDatefin;
    }

    public function setPtgDatefin(?\DateTimeInterface $ptgDatefin): self
    {
        $this->ptgDatefin = $ptgDatefin;
        return $this;
    }

    public function getPtgHeurefin(): ?\DateTimeInterface
    {
        return $this->ptgHeurefin;
    }

    public function setPtgHeurefin(?\DateTimeInterface $ptgHeurefin): self
    {
        $this->ptgHeurefin = $ptgHeurefin;
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

    public function getIdParcelle(): ?Parcelle
    {
        return $this->idParcelle;
    }

    public function setIdParcelle(?Parcelle $idParcelle): self
    {
        $this->idParcelle = $idParcelle;
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

    public function __toString(): string
    {
        return 'Pointage - ' . $this->getIdPointage();
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

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
