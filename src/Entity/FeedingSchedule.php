<?php

namespace App\Entity;

use App\Repository\FeedingScheduleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedingScheduleRepository::class)]
class FeedingSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $intervalDays = null;

    #[ORM\ManyToOne(inversedBy: 'feedingSchedules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Species $species = null;

    #[ORM\ManyToOne(inversedBy: 'feedingSchedules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LifeStage $lifeStage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntervalDays(): ?int
    {
        return $this->intervalDays;
    }

    public function setIntervalDays(int $intervalDays): static
    {
        $this->intervalDays = $intervalDays;

        return $this;
    }

    public function getSpecies(): ?Species
    {
        return $this->species;
    }

    public function setSpecies(?Species $species): static
    {
        $this->species = $species;

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
