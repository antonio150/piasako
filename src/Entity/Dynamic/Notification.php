<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use App\Controller\Dynamic\Notification\CheckDebutTacheNotificationController;
use App\Controller\Dynamic\Notification\DeleteNotificationController;
use App\Controller\Dynamic\Notification\ListeNotificationsPersonneController;
use App\Controller\Dynamic\Notification\UpdateNotificationVuController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Uid\Uuid;

/**
 * Notification
 */
#[ORM\Table(name: 'notification')]
#[ORM\Index(name: 'IDX_Notification_Titre', columns: ['Titre'])]
#[ORM\Index(name: 'IDX_Notification_LienEntity', columns: ['LienEntity'])]
#[ORM\Index(name: 'IDX_Notification_UserCreation', columns: ['user_creation_id'])]
#[ORM\Index(name: 'IDX_Notification_TachesPlanifies', columns: ['taches_planifies_id'])]
#[ORM\Index(name: 'IDX_Notification_Personne', columns: ['personne_id'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class Notification
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\Column(name: 'ID_Notification', type: 'string', length: 36, nullable: false)]
    private ?string $id = null;

    #[ORM\Column(name: 'Titre', type: 'string', length: 255, nullable: true)]
    private ?string $titre;

    #[ORM\Column(name: 'Description', type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(name: 'LienEntity', type: 'string', length: 255, nullable: true)]
    private ?string $lienEntity;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'user_creation_id', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
   
    private Utilisateur $usercreation;

    #[ORM\ManyToOne(targetEntity: Personne::class)]
    #[ORM\JoinColumn(name: 'personne_id', referencedColumnName: 'ID_Personne', nullable: true, onDelete: 'SET NULL')]
    private ?Personne $personne = null;

    #[ORM\ManyToOne(targetEntity: TachesPlanifies::class)]
    #[ORM\JoinColumn(name: 'taches_planifies_id', referencedColumnName: 'ID_Taches_Planifies', nullable: true)]
    private ?TachesPlanifies $tachesPlanifies = null;

    #[ORM\Column(name: 'Vu', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $vu = false;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
        $this->vu = false;
    }

    /**
     * Get the value of id
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the value of titre
     */
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
     * Set the value of titre
     */
    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the value of lienEntity
     */
    public function getLienEntity(): ?string
    {
        return $this->lienEntity;
    }

    /**
     * Set the value of lienEntity
     */
    public function setLienEntity(?string $lienEntity): self
    {
        $this->lienEntity = $lienEntity;
        return $this;
    }

    /**
     * Get the value of usercreation
     */
    public function getUsercreation(): Utilisateur
    {
        return $this->usercreation;
    }

    /**
     * Set the value of usercreation
     */
    public function setUsercreation(Utilisateur $usercreation): self
    {
        $this->usercreation = $usercreation;
        return $this;
    }

    /**
     * Get the value of personne
     */
    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    /**
     * Set the value of personne
     */
    public function setPersonne(?Personne $personne): self
    {
        $this->personne = $personne;
        return $this;
    }

    /**
     * Get the value of tachesPlanifies
     */
    public function getTachesPlanifies(): ?TachesPlanifies
    {
        return $this->tachesPlanifies;
    }

    /**
     * Set the value of tachesPlanifies
     */
    public function setTachesPlanifies(?TachesPlanifies $tachesPlanifies): self
    {
        $this->tachesPlanifies = $tachesPlanifies;
        return $this;
    }

    /**
     * Get the value of vu
     */
    public function isVu(): bool
    {
        return $this->vu;
    }

    /**
     * Set the value of vu
     */
    public function setVu(bool $vu): self
    {
        $this->vu = $vu;
        return $this;
    }
}