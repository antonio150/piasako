<?php

namespace App\Entity\Dynamic;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Symfony\Component\Uid\Uuid;

/**
 * PersonneEquipe
 */
#[ORM\Table(name: 'personne_equipe')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class PersonneEquipe
{
  

    #[ORM\Id]
    #[ORM\Column(name: 'ID_Personne_Equipe', type: 'string', length: 36, unique: true, nullable: false)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Personne::class, inversedBy: 'personneEquipes')]
    #[ORM\JoinColumn(name: 'ID_Personne', referencedColumnName: 'ID_Personne', nullable: true, onDelete: 'CASCADE')]
    private ?Personne $personne = null;

    #[ORM\ManyToOne(targetEntity: Equipe::class, inversedBy: 'personneEquipes')]
    #[ORM\JoinColumn(name: 'ID_Equipe', referencedColumnName: 'ID_Equipe', nullable: false)]
    private ?Equipe $equipe = null;

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): self
    {
        $this->equipe = $equipe;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s - %s', $this->personne ? $this->personne->__toString() : '', $this->equipe ? $this->equipe->__toString() : '');
    }
}