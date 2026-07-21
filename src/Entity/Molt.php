<?php

namespace App\Entity;

use App\Repository\MoltRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MoltRepository::class)]
class Molt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(nullable: true)]
    private ?float $legSpanCm = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(inversedBy: 'molts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Spider $spider = null;

    #[ORM\ManyToOne(inversedBy: 'molts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LifeStage $lifeStage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLegSpanCm(): ?float
    {
        return $this->legSpanCm;
    }

    public function setLegSpanCm(?float $legSpanCm): static
    {
        $this->legSpanCm = $legSpanCm;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getSpider(): ?Spider
    {
        return $this->spider;
    }

    public function setSpider(?Spider $spider): static
    {
        $this->spider = $spider;

        return $this;
    }

    public function getLifeStage(): ?LifeStage
    {
        return $this->lifeStage;
    }

    public function setLifeStage(?LifeStage $lifeStage): static
    {
        $this->lifeStage = $lifeStage;

        return $this;
    }
}
