<?php

namespace App\Entity;

use App\Repository\SpeciesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpeciesRepository::class)]
class Species
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $latinName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commonName = null;

    #[ORM\Column(length: 20)]
    private ?string $habitat = null;

    #[ORM\Column]
    private ?float $tempMin = null;

    #[ORM\Column]
    private ?float $tempMax = null;

    #[ORM\Column]
    private ?float $humidityMin = null;

    #[ORM\Column]
    private ?float $humidityMax = null;

    /**
     * @var Collection<int, Spider>
     */
    #[ORM\OneToMany(targetEntity: Spider::class, mappedBy: 'species')]
    private Collection $spiders;

    /**
     * @var Collection<int, FeedingSchedule>
     */
    #[ORM\OneToMany(targetEntity: FeedingSchedule::class, mappedBy: 'species')]
    private Collection $feedingSchedules;

    public function __construct()
    {
        $this->spiders = new ArrayCollection();
        $this->feedingSchedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLatinName(): ?string
    {
        return $this->latinName;
    }

    public function setLatinName(string $latinName): static
    {
        $this->latinName = $latinName;

        return $this;
    }

    public function getCommonName(): ?string
    {
        return $this->commonName;
    }

    public function setCommonName(?string $commonName): static
    {
        $this->commonName = $commonName;

        return $this;
    }

    public function getHabitat(): ?string
    {
        return $this->habitat;
    }

    public function setHabitat(string $habitat): static
    {
        $this->habitat = $habitat;

        return $this;
    }

    public function getTempMin(): ?float
    {
        return $this->tempMin;
    }

    public function setTempMin(float $tempMin): static
    {
        $this->tempMin = $tempMin;

        return $this;
    }

    public function getTempMax(): ?float
    {
        return $this->tempMax;
    }

    public function setTempMax(float $tempMax): static
    {
        $this->tempMax = $tempMax;

        return $this;
    }

    public function getHumidityMin(): ?float
    {
        return $this->humidityMin;
    }

    public function setHumidityMin(float $humidityMin): static
    {
        $this->humidityMin = $humidityMin;

        return $this;
    }

    public function getHumidityMax(): ?float
    {
        return $this->humidityMax;
    }

    public function setHumidityMax(float $humidityMax): static
    {
        $this->humidityMax = $humidityMax;

        return $this;
    }

    /**
     * @return Collection<int, Spider>
     */
    public function getSpiders(): Collection
    {
        return $this->spiders;
    }

    public function addSpider(Spider $spider): static
    {
        if (!$this->spiders->contains($spider)) {
            $this->spiders->add($spider);
            $spider->setSpecies($this);
        }

        return $this;
    }

    public function removeSpider(Spider $spider): static
    {
        if ($this->spiders->removeElement($spider)) {
            // set the owning side to null (unless already changed)
            if ($spider->getSpecies() === $this) {
                $spider->setSpecies(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FeedingSchedule>
     */
    public function getFeedingSchedules(): Collection
    {
        return $this->feedingSchedules;
    }

    public function addFeedingSchedule(FeedingSchedule $feedingSchedule): static
    {
        if (!$this->feedingSchedules->contains($feedingSchedule)) {
            $this->feedingSchedules->add($feedingSchedule);
            $feedingSchedule->setSpecies($this);
        }

        return $this;
    }

    public function removeFeedingSchedule(FeedingSchedule $feedingSchedule): static
    {
        if ($this->feedingSchedules->removeElement($feedingSchedule)) {
            // set the owning side to null (unless already changed)
            if ($feedingSchedule->getSpecies() === $this) {
                $feedingSchedule->setSpecies(null);
            }
        }

        return $this;
    }
}
