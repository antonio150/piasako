<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;

use App\Controller\Dynamic\PointagePlanification\CreatePointagePlanificationController;
use App\Controller\Dynamic\PointagePlanification\CreatePointagePlanificationMobileController;
use App\Controller\Dynamic\PointagePlanification\CreatePointagePlanificationMobileMultipleController;
use App\Controller\Dynamic\PointagePlanification\CreatePointagePlanificationPourMobileMultipleController;
use App\Controller\Dynamic\PointagePlanification\CreatePointagePlanificationVerificationControllerMobile;
use App\Controller\Dynamic\PointagePlanification\DeletePointagePlanificationController;
use App\Controller\Dynamic\PointagePlanification\ListePointagePlanificationController;
use App\Controller\Dynamic\PointagePlanification\ListePointagePlanificationParStatusController;
use App\Controller\Dynamic\PointagePlanification\UpdatePointagePlanificationController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Uid\Uuid;

/**
 * Pointage
 */
#[ORM\Table(name: 'pointage_planification')]
#[ORM\Index(name: 'WDIDX_Pointage_ID_Taches_Planifies', columns: ['ID_Taches_Planifies'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class PointagePlanification
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'ID_Pointage_Planification', type: 'string', length: 36, unique: true, nullable: true)]
    private ?string $id = null;

    #[ORM\Column(name: 'PTG_DateDebut', type: 'date', nullable: true)]
    private ?\DateTimeInterface $ptgDatedebut = null;

    #[ORM\Column(name: 'PTG_HeureDebut', type: 'time', nullable: true)]
    private ?\DateTimeInterface $ptgHeuredebut = null;

    #[ORM\Column(name: 'PTG_DateFin', type: 'date', nullable: true)]
    private ?\DateTimeInterface $ptgDatefin = null;

    #[ORM\Column(name: 'PTG_HeureFin', type: 'time', nullable: true)]
    private ?\DateTimeInterface $ptgHeurefin = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $usercreation = null;

    #[ORM\ManyToOne(targetEntity: Personne::class)]
    #[ORM\JoinColumn(name: 'ID_Personne', referencedColumnName: 'ID_Personne', nullable: true, onDelete: 'CASCADE')]
    private ?Personne $idPersonne = null;

    #[ORM\ManyToOne(targetEntity: TachesPlanifies::class, inversedBy: 'pointages')]
    #[ORM\JoinColumn(name: 'ID_Taches_Planifies', referencedColumnName: 'ID_Taches_Planifies', nullable: true)]
    private ?TachesPlanifies $tachesPlanifies = null;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    //------------------------------------------------------------------------------------------------------------------

    public function getTimeIntervale() : int
    {
        if (
            $this->ptgDatedebut === null ||
            $this->ptgHeuredebut === null ||
            $this->ptgDatefin === null ||
            $this->ptgHeurefin === null
        ) {
            return 0;
        }

        // Combine date and time for start
        $startDateTime = clone $this->ptgDatedebut;
        

        // Combine date and time for end
        $endDateTime = clone $this->ptgDatefin;
        

        $diffInSeconds = $endDateTime->getTimestamp() - $startDateTime->getTimestamp();

        if ($diffInSeconds < 0) {
            return 0;
        }

        return (int)($diffInSeconds / 60);
    }

    //------------------------------------------------------------------------------------------------------------------

    public function getId(): ?string
    {
        return $this->id;
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

    public function getIdPersonne(): ?Personne
    {
        return $this->idPersonne;
    }

    public function setIdPersonne(?Personne $idPersonne): self
    {
        $this->idPersonne = $idPersonne;
        return $this;
    }

    public function getTachesPlanifies(): ?TachesPlanifies
    {
        return $this->tachesPlanifies;
    }

    public function setTachesPlanifies(?TachesPlanifies $tachesPlanifies): self
    {
        $this->tachesPlanifies = $tachesPlanifies;
        return $this;
    }

    public function __toString(): string
    {
        return 'Pointage - ' . $this->getId();
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