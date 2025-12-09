<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use App\State\DynamicDataProvider;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\CollectionOperation;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Controller\Dynamic\Utilisateur\CreateUtilisateurController;
use App\Controller\Dynamic\Utilisateur\DeleteUtilisateurController;
use App\Controller\Dynamic\Utilisateur\ExportUtilisateurAgentController;
use App\Controller\Dynamic\Utilisateur\ListeUtilisateurAdminController;
use App\Controller\Dynamic\Utilisateur\ListeUtilisateurController;
use App\Controller\Dynamic\Utilisateur\MeUtilisateurSimpleController;
use App\Controller\Dynamic\Utilisateur\OneUtilisateurController;
use App\Controller\Dynamic\Utilisateur\RemoveUtilisateurController;
use App\Controller\Dynamic\Utilisateur\UpdateUtilisateurController;
use App\Controller\UserController;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity()]
#[ORM\Table(name: '`user`')]
// #[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', columns: ['USER_Login'])]
#[ORM\HasLifecycleCallbacks]

class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\Column(type: 'string', unique: true, name: 'ID_User_Site', length: 36)]
    private ?string $id = null;

    #[ORM\Column(name: 'USER_Login', type: 'string', length: 50, nullable: true)]
    private ?string $userLogin = null;

   
    #[ORM\Column(name: 'USER_MotDePasse', type: 'string', length: 255, nullable: true)]
    private ?string $userMotDePasse = null;

    #[ORM\OneToOne(targetEntity: Personne::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'ID_Personne', referencedColumnName: 'ID_Personne', nullable: true, onDelete: 'CASCADE')]
    private ?Personne $personne = null;

 
    #[ORM\Column(name: 'USER_Inactif', type: 'boolean', nullable: true)]
    private ?bool $userInactif = false;

    #[ORM\Column(name: 'SIT_Code', type: 'string', length: 50, nullable: true)]
    private ?string $sitCode;

    #[ORM\Column(type: 'datetime')]
     private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    
    
    
    // Add OneToMany relationships
    #[ORM\OneToMany(mappedBy: 'usercreation', targetEntity: Personne::class)]
    private Collection $personnes;

    #[ORM\OneToMany(mappedBy: 'usercreation', targetEntity: Parcelle::class)]
    private Collection $parcelles;

    #[ORM\OneToMany(mappedBy: 'usercreation', targetEntity: Taches::class)]
    private Collection $taches;

    #[ORM\OneToMany(mappedBy: 'usercreation', targetEntity: TachesPlanifies::class)]
    private Collection $tachesPlanifies;

    #[ORM\OneToMany(mappedBy: 'usercreation', targetEntity: TachesTravailleur::class)]
    private Collection $tachesTravailleurs;

    #[ORM\OneToMany(mappedBy: 'usercreation', targetEntity: TachesIncidents::class)]
    private Collection $tachesIncidents;

    #[ORM\OneToMany(mappedBy: 'usercreation', targetEntity: TachesHistoriques::class)]
    private Collection $tachesHistoriques;

    #[ORM\OneToMany(mappedBy: 'usercreation', targetEntity: TachesPriorites::class)]
    private Collection $tachesPriorites;

    #[ORM\OneToMany(mappedBy: 'usercreation', targetEntity: TachesStatut::class)]
    private Collection $tachesStatuts;

    #[ORM\OneToMany(mappedBy: 'usercreation', targetEntity: TachesType::class)]
    private Collection $tachesTypes;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->personne = null;
        $this->userInactif = false;
        $this->sitCode = null;
        $this->personnes = new ArrayCollection();
        $this->parcelles = new ArrayCollection();
        $this->taches = new ArrayCollection();
        $this->tachesPlanifies = new ArrayCollection();
        $this->tachesTravailleurs = new ArrayCollection();
        $this->tachesIncidents = new ArrayCollection();
        $this->tachesHistoriques = new ArrayCollection();
        $this->tachesPriorites = new ArrayCollection();
        $this->tachesStatuts = new ArrayCollection();
        $this->tachesTypes = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserLogin(): ?string
    {
        return $this->userLogin;
    }

    public function setUserLogin(string $userLogin): static
    {
        $this->userLogin = $userLogin;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->userLogin;
    }

    public function getRoles(): array
    {
        return ['ROLE_AGENT'];
    }

   
    public function setUserMotDePasse(string $UserMotDePasse): static
    {
        $this->userMotDePasse = $UserMotDePasse;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->userMotDePasse;
    }

    public function eraseCredentials(): void
    {
    }


    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(?Personne $personne): self
    {
        $this->personne = $personne;
        return $this;
    }

   
    public function isUserInactif(): bool
    {
        return $this->userInactif;
    }

    public function setUserInactif(bool $userInactif): static
    {
        $this->userInactif = $userInactif;
        return $this;
    }

    public function getSitCode(): ?string
    {
        return $this->sitCode;
    }

    public function setSitCode(string $sitCode): static
    {
        $this->sitCode = $sitCode;
        return $this;
    }


    // Add getters and setters for personnes
    public function getPersonnes(): Collection
    {
        return $this->personnes;
    }

    public function addPersonne(Personne $personne): self
    {
        if (!$this->personnes->contains($personne)) {
            $this->personnes[] = $personne;
            $personne->setUserCreation($this);
        }
        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->personnes->removeElement($personne)) {
            if ($personne->getUserCreation() === $this) {
                $personne->setUserCreation(null);
            }
        }
        return $this;
    }

    // Add getters and setters for parcelles
    public function getParcelles(): Collection
    {
        return $this->parcelles;
    }

    public function addParcelle(Parcelle $parcelle): self
    {
        if (!$this->parcelles->contains($parcelle)) {
            $this->parcelles[] = $parcelle;
            $parcelle->setUserCreation($this);
        }
        return $this;
    }

    public function removeParcelle(Parcelle $parcelle): self
    {
        if ($this->parcelles->removeElement($parcelle)) {
            if ($parcelle->getUserCreation() === $this) {
                $parcelle->setUserCreation(null);
            }
        }
        return $this;
    }

    // Add getters and setters for taches
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Taches $tache): self
    {
        if (!$this->taches->contains($tache)) {
            $this->taches[] = $tache;
            $tache->setUsercreation($this);
        }
        return $this;
    }

    public function removeTache(Taches $tache): self
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getUsercreation() === $this) {
                $tache->setUsercreation(null);
            }
        }
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
            $tachesPlanifie->setUsercreation($this);
        }
        return $this;
    }

    public function removeTachesPlanifie(TachesPlanifies $tachesPlanifie): self
    {
        if ($this->tachesPlanifies->removeElement($tachesPlanifie)) {
            if ($tachesPlanifie->getUsercreation() === $this) {
                $tachesPlanifie->setUsercreation(null);
            }
        }
        return $this;
    }

    // Add getters and setters for tachesTravailleurs
    public function getTachesTravailleurs(): Collection
    {
        return $this->tachesTravailleurs;
    }

    public function addTachesTravailleur(TachesTravailleur $tachesTravailleur): self
    {
        if (!$this->tachesTravailleurs->contains($tachesTravailleur)) {
            $this->tachesTravailleurs[] = $tachesTravailleur;
            $tachesTravailleur->setUsercreation($this);
        }
        return $this;
    }

    public function removeTachesTravailleur(TachesTravailleur $tachesTravailleur): self
    {
        if ($this->tachesTravailleurs->removeElement($tachesTravailleur)) {
            if ($tachesTravailleur->getUsercreation() === $this) {
                $tachesTravailleur->setUsercreation(null);
            }
        }
        return $this;
    }

    // Add getters and setters for tachesIncidents
    public function getTachesIncidents(): Collection
    {
        return $this->tachesIncidents;
    }

    public function addTachesIncident(TachesIncidents $tachesIncident): self
    {
        if (!$this->tachesIncidents->contains($tachesIncident)) {
            $this->tachesIncidents[] = $tachesIncident;
            $tachesIncident->setUsercreation($this);
        }
        return $this;
    }

    public function removeTachesIncident(TachesIncidents $tachesIncident): self
    {
        if ($this->tachesIncidents->removeElement($tachesIncident)) {
            if ($tachesIncident->getUsercreation() === $this) {
                $tachesIncident->setUsercreation(null);
            }
        }
        return $this;
    }

    // Add getters and setters for tachesHistoriques
    public function getTachesHistoriques(): Collection
    {
        return $this->tachesHistoriques;
    }

    public function addTachesHistorique(TachesHistoriques $tachesHistorique): self
    {
        if (!$this->tachesHistoriques->contains($tachesHistorique)) {
            $this->tachesHistoriques[] = $tachesHistorique;
            $tachesHistorique->setUsercreation($this);
        }
        return $this;
    }

    public function removeTachesHistorique(TachesHistoriques $tachesHistorique): self
    {
        if ($this->tachesHistoriques->removeElement($tachesHistorique)) {
            if ($tachesHistorique->getUsercreation() === $this) {
                $tachesHistorique->setUsercreation(null);
            }
        }
        return $this;
    }

    // Add getters and setters for tachesPriorites
    public function getTachesPriorites(): Collection
    {
        return $this->tachesPriorites;
    }

    public function addTachesPriorite(TachesPriorites $tachesPriorite): self
    {
        if (!$this->tachesPriorites->contains($tachesPriorite)) {
            $this->tachesPriorites[] = $tachesPriorite;
            $tachesPriorite->setUsercreation($this);
        }
        return $this;
    }

    public function removeTachesPriorite(TachesPriorites $tachesPriorite): self
    {
        if ($this->tachesPriorites->removeElement($tachesPriorite)) {
            if ($tachesPriorite->getUsercreation() === $this) {
                $tachesPriorite->setUsercreation(null);
            }
        }
        return $this;
    }

    // Add getters and setters for tachesStatuts
    public function getTachesStatuts(): Collection
    {
        return $this->tachesStatuts;
    }

    public function addTachesStatut(TachesStatut $tachesStatut): self
    {
        if (!$this->tachesStatuts->contains($tachesStatut)) {
            $this->tachesStatuts[] = $tachesStatut;
            $tachesStatut->setUsercreation($this);
        }
        return $this;
    }

    public function removeTachesStatut(TachesStatut $tachesStatut): self
    {
        if ($this->tachesStatuts->removeElement($tachesStatut)) {
            if ($tachesStatut->getUsercreation() === $this) {
                $tachesStatut->setUsercreation(null);
            }
        }
        return $this;
    }

    // Add getters and setters for tachesTypes
    public function getTachesTypes(): Collection
    {
        return $this->tachesTypes;
    }

    public function addTachesType(TachesType $tachesType): self
    {
        if (!$this->tachesTypes->contains($tachesType)) {
            $this->tachesTypes[] = $tachesType;
            $tachesType->setUsercreation($this);
        }
        return $this;
    }

    public function removeTachesType(TachesType $tachesType): self
    {
        if ($this->tachesTypes->removeElement($tachesType)) {
            if ($tachesType->getUsercreation() === $this) {
                $tachesType->setUsercreation(null);
            }
        }
        return $this;
    }
}
