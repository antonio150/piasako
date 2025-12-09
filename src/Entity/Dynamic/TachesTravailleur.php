<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\Dynamic\TacheTravailleur\AssigneTachesTravailleurMobileController;
use App\Controller\Dynamic\TacheTravailleur\CreateTachesTravailleurController;
use App\Controller\Dynamic\TacheTravailleur\CreateTachesTravailleurMobileController;
use App\Controller\Dynamic\TacheTravailleur\DeleteTachesTravailleurController;
use App\Controller\Dynamic\TacheTravailleur\DeleteTachesTravailleurMobileController;
use App\Controller\Dynamic\TacheTravailleur\ListeTachesTravailleurController;
use App\Controller\Dynamic\TacheTravailleur\listeTravailleurController;
use App\Controller\Dynamic\TacheTravailleur\OneTachesTravailleurController;
use App\Controller\Dynamic\TacheTravailleur\PersonByTachesTravailleurController;
use App\Controller\Dynamic\TacheTravailleur\UpdateTachesTravailleurController;
use App\Controller\Dynamic\TacheTravailleur\UtilsTachesTravailleurController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Dynamic\Personne;
use App\Entity\Dynamic\TachesPlanifies;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * TachesTravailleur
 */
#[ORM\Table(name: 'taches_travailleur')]
#[ORM\Index(name: 'WDIDX_Taches_Travailleur_ID_Taches_Planifies', columns: ['ID_Taches_Planifies'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class TachesTravailleur
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\Column(name: 'ID_Taches_Travailleur', unique: true, type: 'string', length: 36, nullable: false)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
    private Utilisateur $usercreation;

    #[ORM\ManyToOne(targetEntity: TachesPlanifies::class)]
    #[ORM\JoinColumn(name: 'ID_Taches_Planifies', referencedColumnName: 'ID_Taches_Planifies')]
    private ?TachesPlanifies $idTachesPlanifies;

    #[ORM\ManyToOne(targetEntity: Personne::class, inversedBy: 'tachesTravailleurs')]
    #[ORM\JoinColumn(name: 'ID_Personne', referencedColumnName: 'ID_Personne', nullable: true, onDelete: 'SET NULL')]
    private ?Personne $personne = null;

    #[ORM\Column(name: 'TTR_Inactif', type: 'boolean', nullable: false)]
    private ?bool $travailleurInactif = false;

    #[ORM\Column(name: 'TTR_DateDebutTravailleur', type: 'datetime', nullable: true)]
    private ?\DateTime $tplDateDebutTravailleur;
  
    #[ORM\Column(name: 'TTR_DateFinTravailleur', type: 'datetime', nullable: true)]
    private ?\DateTime $tplDateFinTravailleur;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function isTravailleurInactif(): ?bool
    {
        return $this->travailleurInactif;
    }

    public function setTravailleurInactif(bool $travailleurInactif): self
    {
        $this->travailleurInactif = $travailleurInactif;
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

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(Personne $personne): self
    {
        $this->personne = $personne;
        return $this;
    }

    public function getTplDateDebutTravailleur(): ?\DateTime
    {
        return $this->tplDateDebutTravailleur;
    }

    public function setTplDateDebutTravailleur(\DateTime $tplDateDebutTravailleur): self
    {
        $this->tplDateDebutTravailleur = $tplDateDebutTravailleur;
        return $this;
    }

    public function getTplDateFinTravailleur(): ?\DateTime
    {
        return $this->tplDateFinTravailleur;
    }

    public function setTplDateFinTravailleur(\DateTime $tplDateFinTravailleur): self
    {
        $this->tplDateFinTravailleur = $tplDateFinTravailleur;
        return $this;
    }

    public function removePersonne(): self
    {
        $this->personne = null;
        return $this;
    }
}