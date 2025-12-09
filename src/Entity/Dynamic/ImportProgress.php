<?php

namespace App\Entity\Dynamic;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\Dynamic\Utilisateur\ImportProgressController;
use App\Entity\Traits\TimestampableTrait as TraitsTimestampableTrait;

#[ORM\Table(name: 'import_progress')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class ImportProgress
{
    use TraitsTimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'string', length: 36, nullable: false)]
    private ?string $id = null;

    #[ORM\Column(name: 'total_rows', type: 'integer', nullable: false)]
    private int $totalRows = 0;

    #[ORM\Column(name: 'success_count', type: 'integer', nullable: false)]
    private int $successCount = 0;

    #[ORM\Column(name: 'errors', type: 'json', nullable: true)]
    private ?array $errors = [];

    #[ORM\Column(name: 'status', type: 'string', length: 50, nullable: false)]
    private string $status = 'pending';

    public function __construct()
    {
        $this->id = Uuid::v4()->toRfc4122();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function setTotalRows(int $totalRows): self
    {
        $this->totalRows = $totalRows;
        return $this;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function setSuccessCount(int $successCount): self
    {
        $this->successCount = $successCount;
        return $this;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function setErrors(?array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}