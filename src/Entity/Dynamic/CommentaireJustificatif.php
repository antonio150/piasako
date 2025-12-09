<?php

namespace App\Entity\Dynamic;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Uid\Uuid;
use App\Entity\Dynamic\TachesPlanifies;
use App\Entity\Dynamic\Utilisateur;

/**
 * CommentaireJustificatif
 */
#[ORM\Table(name: 'commentaire_justificatif')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class CommentaireJustificatif
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'ID_Commentaire_Justificatif', unique: true, type: 'string', length: 36, nullable: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: TachesPlanifies::class, inversedBy: 'commentairesJustificatifs')]
    #[ORM\JoinColumn(name: 'ID_Taches_Planifies', referencedColumnName: 'ID_Taches_Planifies', nullable: true)]
    private ?TachesPlanifies $tachePlanifie;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'ID_User', referencedColumnName: 'ID_User_Site', nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $user;

    #[ORM\Column(name: 'CJ_Commentaire', type: 'text', nullable: true)]
    private string $commentaire;

    #[ORM\Column(name: 'CJ_Type_Action', type: 'string', length: 50, nullable: true)]
    private string $typeAction;

    #[ORM\Column(name: 'CJ_Nb_Travailleur_Requis', type: 'integer', nullable: true)]
    private ?int $nbTravailleurRequis = null;

    #[ORM\Column(name: 'CJ_Date_Debut', type: 'datetime', nullable: true)]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column(name: 'CJ_Date_Fin', type: 'datetime', nullable: true)]
    private ?\DateTime $dateFin = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
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

    public function getTachePlanifie(): ?TachesPlanifies
    {
        return $this->tachePlanifie;
    }

    public function setTachePlanifie(?TachesPlanifies $tachePlanifie): self
    {
        $this->tachePlanifie = $tachePlanifie;
        return $this;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCommentaire(): string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getTypeAction(): string
    {
        return $this->typeAction;
    }

    public function setTypeAction(string $typeAction): self
    {
        $this->typeAction = $typeAction;
        return $this;
    }

    public function getNbTravailleurRequis(): ?int
    {
        return $this->nbTravailleurRequis;
    }

    public function setNbTravailleurRequis(?int $nbTravailleurRequis): self
    {
        $this->nbTravailleurRequis = $nbTravailleurRequis;
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

    public function __toString(): string
    {
        return $this->commentaire;
    }
}