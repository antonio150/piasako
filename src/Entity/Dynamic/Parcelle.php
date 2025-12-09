<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\Dynamic\Parcelle\CreateParcelleController;
use App\Controller\Dynamic\Parcelle\DeleteParcelleController;
use App\Controller\Dynamic\Parcelle\ListeParcelleController;
use App\Controller\Dynamic\Parcelle\OneParcelleController;
use App\Controller\Dynamic\Parcelle\UpdateParcelleController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

/**
 * Parcelle
 */
#[ORM\Table(name: 'parcelle')]
#[ORM\Index(name: 'WDIDX_Parcelle_PAR_Nom', columns: ['PAR_Nom'])]
#[ORM\Index(name: 'WDIDX_Parcelle_PAR_Code', columns: ['PAR_Code'])]
#[ORM\UniqueConstraint(name: 'PAR_Code', columns: ['PAR_Code'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class Parcelle
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\Column(name: 'ID_Parcelle', type: 'string', length: 36, nullable: false)]
    private ?string $idParcelle = null;

    #[ORM\Column(name: 'PAR_Nom', type: 'string', length: 50, nullable: false)]
    private $parNom;

    #[ORM\Column(name: 'PAR_Code', type: 'string', length: 50, nullable: false)]
    private $parCode;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'user_creation_id', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]

    private Utilisateur $usercreation;

    #[ORM\OneToMany(mappedBy: 'idParcelle', targetEntity: Pointage::class)]
    private Collection $pointages;

    #[ORM\OneToMany(mappedBy: 'idParcelle', targetEntity: TachesPlanifies::class)]
    private Collection $tachesPlanifies;

    public function __construct()
    {
        $this->idParcelle = Uuid::v4()->toRfc4122();
        $this->pointages = new ArrayCollection();
        $this->tachesPlanifies = new ArrayCollection();
    }

    public function getIdParcelle(): ?string
    {
        return $this->idParcelle;
    }

    public function getParNom(): ?string
    {
        return $this->parNom;
    }

    public function setParNom(string $parNom): self
    {
        $this->parNom = $parNom;
        return $this;
    }

    public function getParCode(): ?string
    {
        return $this->parCode;
    }

    public function setParCode(string $parCode): self
    {
        $this->parCode = $parCode;
        return $this;
    }

    public function getUserCreation(): ?Utilisateur
    {
        return $this->usercreation;
    }

    public function setUserCreation(?Utilisateur $usercreation): self
    {
        $this->usercreation = $usercreation;
        return $this;
    }

    public function getPointages(): Collection
    {
        return $this->pointages;
    }

    public function addPointage(Pointage $pointage): self
    {
        if (!$this->pointages->contains($pointage)) {
            $this->pointages[] = $pointage;
            $pointage->setIdParcelle($this);
        }
        return $this;
    }

    public function removePointage(Pointage $pointage): self
    {
        if ($this->pointages->removeElement($pointage)) {
            if ($pointage->getIdParcelle() === $this) {
                $pointage->setIdParcelle(null);
            }
        }
        return $this;
    }

    public function getTachesPlanifies(): Collection
    {
        return $this->tachesPlanifies;
    }

    public function addTachesPlanifie(TachesPlanifies $tachesPlanifie): self
    {
        if (!$this->tachesPlanifies->contains($tachesPlanifie)) {
            $this->tachesPlanifies[] = $tachesPlanifie;
            $tachesPlanifie->setIdParcelle($this);
        }
        return $this;
    }

    public function removeTachesPlanifie(TachesPlanifies $tachesPlanifie): self
    {
        if ($this->tachesPlanifies->removeElement($tachesPlanifie)) {
            if ($tachesPlanifie->getIdParcelle() === $this) {
                $tachesPlanifie->setIdParcelle(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->getParNom() . ' (' . $this->getParCode() . ')';
    }
}