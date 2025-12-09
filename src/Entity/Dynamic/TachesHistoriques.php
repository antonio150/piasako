<?php

namespace App\Entity\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Dynamic\TacheHistorique\CreateTachesHistoriqueController;
use App\Controller\Dynamic\TacheHistorique\DeleteTachesHistoriqueController;
use App\Controller\Dynamic\TacheHistorique\ListeTachesHistoriqueController;
use App\Controller\Dynamic\TacheHistorique\OneTachesHistoriqueController;
use App\Controller\Dynamic\TacheHistorique\UpdateTachesHistoriqueController;
use App\Controller\Dynamic\TacheHistorique\UtilsTachesHistoriqueController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Dynamic\User;
use App\Entity\Dynamic\TachesPlanifies;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Uid\Uuid;

/**
 * TachesHistoriques
 */
#[ORM\Table(name: 'taches_historiques')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class TachesHistoriques
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'ID_Taches_Historiques', unique: true, type: 'string', length: 36, nullable: false)]
    private ?string $id = null;

    #[ORM\Column(name: 'THT_Date', type: 'date', nullable: false)]
    private \DateTimeInterface $thtDate;

    #[ORM\Column(name: 'THT_Commentaire', type: 'text', nullable: false)]
    private string $thtCommentaire;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'UserCreation', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
    private Utilisateur $usercreation;

    #[ORM\ManyToOne(targetEntity: TachesPlanifies::class)]
    #[ORM\JoinColumn(name: 'ID_Taches_Planifies', referencedColumnName: 'ID_Taches_Planifies')]
   private ?TachesPlanifies $idTachesPlanifies = null;

    #[ORM\Column(name: 'THT_Heure', type: 'time', nullable: false)]
    private \DateTimeInterface $thtHeure;

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

    public function getThtDate(): \DateTimeInterface
    {
        return $this->thtDate;
    }

    public function setThtDate(\DateTimeInterface $thtDate): self
    {
        $this->thtDate = $thtDate;
        return $this;
    }

    public function getThtCommentaire(): string
    {
        return $this->thtCommentaire;
    }

    public function setThtCommentaire(string $thtCommentaire): self
    {
        $this->thtCommentaire = $thtCommentaire;
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

    public function getThtHeure(): \DateTimeInterface
    {
        return $this->thtHeure;
    }

    public function setThtHeure(\DateTimeInterface $thtHeure): self
    {
        $this->thtHeure = $thtHeure;
        return $this;
    }

    public function __toString(): string
    {
        return 'Historique de tâche - ' . ($this->id ?: 'N/A');
    }
}