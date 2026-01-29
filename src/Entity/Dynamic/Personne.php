<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\Dynamic\Personne\UpdatePersonneController;
use App\Controller\Dynamic\Personne\CreatePersonneController;
use App\Controller\Dynamic\Personne\DeletePersonneController;
use App\Controller\Dynamic\Personne\ListePersonneAgentActifController;
use App\Controller\Dynamic\Personne\ListePersonneAgentMobileController;
use App\Controller\Dynamic\Utilisateur\ImportPersonneAgentController;
use App\Controller\Dynamic\Personne\ListePersonneController;
use App\Controller\Dynamic\Personne\ListePersonnePaginateController;
use App\Controller\Dynamic\Personne\ListePersonnePasAgentController;
use App\Controller\Dynamic\Personne\OnePersonneController;
use App\Controller\Dynamic\TacheTravailleur\ListePersonnesAvecAssignationController;
use App\Controller\Dynamic\Personne\PersonneTachesController;
use App\Controller\Dynamic\Personne\PersonneTachesSansDoublureController;
use App\Controller\Dynamic\Personne\RapportPersonneController;
use App\Controller\Dynamic\Personne\RapportPersonneExportController;
use App\Controller\Dynamic\TachePlanifies\ListePersonnesByPointagePlanificationController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Dynamic\User;
use App\Entity\Dynamic\FileStock;
use App\Entity\Traits\TimestampableTrait;
use App\Enum\Sex;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

/**
 * Personne
 */
#[ORM\Table(name: 'personne')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity]

class Personne
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\Column(name: 'ID_Personne', type: 'string', length: 36, nullable: false)]
    private ?string $id = null;

    #[ORM\Column(name: 'PERS_Nom', type: 'string', length: 50, nullable: false)]
    private ?string $persNom;

    #[ORM\Column(name: 'PERS_Prenom', type: 'string', length: 50, nullable: true)]
    private ?string $persPrenom = null;

    #[ORM\Column(name: 'PERS_Matricule', type: 'string', length: 50, nullable: true)]
    private ?string $persMatricule;

    #[ORM\Column(name: 'PERS_Contact', type: 'string', length: 50, nullable: true)]
    private ?string $persContact = null;

    #[ORM\Column(name: 'PERS_Adresse', type: 'text', nullable: true)]
    private ?string $persAdresse;

    #[ORM\Column(name: 'PERS_Mail', type: 'string', length: 255, nullable: true)]
    private ?string $persMail;

    #[ORM\Column(name: 'PERS_Num_Badge', type: 'string', length: 50, nullable: true)]
    private ?string $persNumBadge;

    #[ORM\Column(name: 'PERS_Token_Devices', type: 'json', nullable: true)]
     private array $tokenDevices = [];

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'CASCADE')]
    private ?Utilisateur $usercreation;

    #[ORM\Column(name: 'PERS_Actif', type: 'boolean', nullable: true)]
    private bool $estActif = true;

    #[ORM\Column(name: 'PERS_DateEmbauche', type: 'date', nullable: true)]
    private ?\DateTimeInterface $persDateembauche;

    #[ORM\Column(name: 'PERS_FinContrat', type: 'date', nullable: true)]
    private ?\DateTimeInterface $persFincontrat;

    #[ORM\Column(name: 'PERS_Sex', enumType: Sex::class, nullable: true)]
    private ?Sex $persSex;

    #[ORM\OneToOne(mappedBy: 'personne', targetEntity: Utilisateur::class, cascade: ['remove'])]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'idPersonne', targetEntity: Pointage::class)]
    private Collection $pointages;

    #[ORM\OneToMany(mappedBy: 'idPersonne', targetEntity: Pointage::class)]
    private Collection $pointagesPlanification;

    #[ORM\OneToMany(mappedBy: 'idPersonne', targetEntity: TachesIncidents::class)]
    private Collection $tachesIncidents;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: PersonneEquipe::class)]
    private Collection $personneEquipes;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: TachesTravailleur::class)]
    private Collection $tachesTravailleurs;


    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

   
    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: Notification::class)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'idPersonne', targetEntity: TachesPlanifies::class)]
    private Collection $tachesPlanifies;

    #[ORM\Column(name: 'PERS_Num_CIN', type: 'string', length: 50, nullable: true)]
    private ?string $persNumCIN;

    #[ORM\Column(type: 'string',nullable: true)]
    private ?string $persPhotoRelative;

    #[ORM\Column(type: 'string',nullable: true)]
    private ?string $persPhotoAbsolute;

    #[ORM\Column(type: 'boolean')]
    private bool $temporary = true;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->pointages = new ArrayCollection();
        $this->pointagesPlanification = new ArrayCollection();
        $this->tachesIncidents = new ArrayCollection();
        $this->personneEquipes = new ArrayCollection();
        $this->tachesTravailleurs = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->tachesPlanifies = new ArrayCollection();
        $this->tokenDevices = [];
    }

   
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPersNom(): ?string
    {
        return $this->persNom;
    }

    public function setPersNom(string $persNom): self
    {
        $this->persNom = $persNom;
        return $this;
    }

    public function getPersPrenom(): ?string
    {
        return $this->persPrenom;
    }

    public function setPersPrenom(string $persPrenom): self
    {
        $this->persPrenom = $persPrenom;
        return $this;
    }

    public function getPersMatricule(): ?string
    {
        return $this->persMatricule;
    }

    public function setPersMatricule(string $persMatricule): self
    {
        $this->persMatricule = $persMatricule;
        return $this;
    }

  
    public function getPersContact(): ?string
    {
        return $this->persContact;
    }

    public function setPersContact(string $persContact): self
    {
        $this->persContact = $persContact;
        return $this;
    }

    public function getPersAdresse(): ?string
    {
        return $this->persAdresse;
    }

    public function setPersAdresse(?string $persAdresse): self
    {
        $this->persAdresse = $persAdresse;
        return $this;
    }

    public function getPersMail(): ?string
    {
        return $this->persMail;
    }

    public function setPersMail(?string $persMail): self
    {
        $this->persMail = $persMail;
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

    public function isestActif(): ?bool
    {
        return $this->estActif;
    }

    public function setestActif(bool $estActif): self
    {
        $this->estActif = $estActif;
        return $this;
    }

    public function getPersDateEmbauche(): ?\DateTimeInterface
    {
        return $this->persDateembauche;
    }

    public function setPersDateEmbauche(?\DateTimeInterface $persDateembauche): self
    {
        $this->persDateembauche = $persDateembauche;
        return $this;
    }

    public function getPersFincontrat(): ?\DateTimeInterface
    {
        return $this->persFincontrat;
    }

    public function setPersFincontrat(?\DateTimeInterface $persFincontrat): self
    {
        $this->persFincontrat = $persFincontrat;
        return $this;
    }
    
    public function getPersNumBadge(): ?string
    {
        return $this->persNumBadge;
    }

    public function setPersNumBadge(?string $persNumBadge): self
    {
        $this->persNumBadge = $persNumBadge;
        return $this;
    }

    public function getTokenDevices(): array
    {
        return $this->tokenDevices;
    }

    public function setTokenDevices(array $tokenDevices): self
    {
        $this->tokenDevices = $tokenDevices;
        return $this;
    }

    public function getPersSex(): ?Sex
    {
        return $this->persSex;
    }

    public function setPersSex(?Sex $persSex): self
    {
        $this->persSex = $persSex;
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
            $pointage->setIdPersonne($this);
        }
        return $this;
    }

    public function removePointage(Pointage $pointage): self
    {
        if ($this->pointages->removeElement($pointage)) {
            if ($pointage->getIdPersonne() === $this) {
                $pointage->setIdPersonne(null);
            }
        }
        return $this;
    }

    public function getPointagesPlanification(): Collection
    {
        return $this->pointagesPlanification;
    }

    public function addPointagePlanification(Pointage $pointagesPlanification): self
    {
        if (!$this->pointages->contains($pointagesPlanification)) {
            $this->pointages[] = $pointagesPlanification;
            $pointagesPlanification->setIdPersonne($this);
        }
        return $this;
    }

    public function removePointagePlanification(Pointage $pointagesPlanification): self
    {
        if ($this->pointages->removeElement($pointagesPlanification)) {
            if ($pointagesPlanification->getIdPersonne() === $this) {
                $pointagesPlanification->setIdPersonne(null);
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
            $tachesIncident->setIdPersonne($this);
        }
        return $this;
    }

    public function removeTachesIncident(TachesIncidents $tachesIncident): self
    {
        if ($this->tachesIncidents->removeElement($tachesIncident)) {
            if ($tachesIncident->getIdPersonne() === $this) {
                $tachesIncident->setIdPersonne(null);
            }
        }
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        if ($this->utilisateur !== $utilisateur) {
            $oldUtilisateur = $this->utilisateur;
            $this->utilisateur = $utilisateur;
            if ($oldUtilisateur !== null && $oldUtilisateur->getPersonne() === $this) {
                $oldUtilisateur->setPersonne(null);
            }
            if ($utilisateur !== null && $utilisateur->getPersonne() !== $this) {
                $utilisateur->setPersonne($this);
            }
        }
        return $this;
    }

  
    public function getPersonneEquipes(): Collection
    {
        return $this->personneEquipes;
    }

    public function addPersonneEquipe(PersonneEquipe $personneEquipe): self
    {
        if (!$this->personneEquipes->contains($personneEquipe)) {
            $this->personneEquipes[] = $personneEquipe;
            $personneEquipe->setPersonne($this);
        }
        return $this;
    }

    public function removePersonneEquipe(PersonneEquipe $personneEquipe): self
    {
        if ($this->personneEquipes->removeElement($personneEquipe)) {
            if ($personneEquipe->getPersonne() === $this) {
                $personneEquipe->setPersonne(null);
            }
        }
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
        }
        return $this;
    }

    public function removeTachesTravailleur(TachesTravailleur $tachesTravailleur): self
    {
        $this->tachesTravailleurs->removeElement($tachesTravailleur);
        return $this;
    }

    public function getPersPhotoRelative(): ?String
    {
        return $this->persPhotoRelative;
    }

    public function setPersPhotoRelative(?String $persPhotoRelative): self
    {
        $this->persPhotoRelative = $persPhotoRelative;
        return $this;
    }

    public function getPersPhotoAbsolute(): ?String
    {
        return $this->persPhotoAbsolute;
    }

    public function setPersPhotoAbsolute(?String $persPhotoAbsolute): self
    {
        $this->persPhotoAbsolute = $persPhotoAbsolute;
        return $this;
    }

    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setPersonne($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getPersonne() === $this) {
                $notification->setPersonne(null);
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
            $tachesPlanifie->setIdPersonne($this);
        }
        return $this;
    }

    public function removeTachesPlanifie(TachesPlanifies $tachesPlanifie): self
    {
        if ($this->tachesPlanifies->removeElement($tachesPlanifie)) {
            if ($tachesPlanifie->getIdPersonne() === $this) {
                $tachesPlanifie->setIdPersonne(null);
            }
        }
        return $this;
    }

    public function getPersNumCIN(): ?string
    {
        return $this->persNumCIN;
    }

    public function setPersNumCIN(?string $persNumCIN): self
    {
        $this->persNumCIN = $persNumCIN;
        return $this;
    }

    public function isTemporary(): bool
    {
        return $this->temporary;
    }

    public function setIsTemporary(bool $temporary): static
    {
        $this->temporary = $temporary;
        return $this;
    }



    public function __toString(): string
    {
        return $this->getPersPrenom() . ' ' . $this->getPersNom();
    }
}