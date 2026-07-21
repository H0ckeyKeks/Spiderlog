<?php

namespace App\Entity;

use App\Repository\LifeStageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LifeStageRepository::class)]
class LifeStage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Spider>
     */
    #[ORM\OneToMany(targetEntity: Spider::class, mappedBy: 'initialLifeStage')]
    private Collection $spiders;

    /**
     * @var Collection<int, FeedingSchedule>
     */
    #[ORM\OneToMany(targetEntity: FeedingSchedule::class, mappedBy: 'lifeStage')]
    private Collection $feedingSchedules;

    /**
     * @var Collection<int, Molt>
     */
    #[ORM\OneToMany(targetEntity: Molt::class, mappedBy: 'lifeStage')]
    private Collection $molts;

    public function __construct()
    {
        $this->spiders = new ArrayCollection();
        $this->feedingSchedules = new ArrayCollection();
        $this->molts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $spider->setInitialLifeStage($this);
        }

        return $this;
    }

    public function removeSpider(Spider $spider): static
    {
        if ($this->spiders->removeElement($spider)) {
            // set the owning side to null (unless already changed)
            if ($spider->getInitialLifeStage() === $this) {
                $spider->setInitialLifeStage(null);
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
            $feedingSchedule->setLifeStage($this);
        }

        return $this;
    }

    public function removeFeedingSchedule(FeedingSchedule $feedingSchedule): static
    {
        if ($this->feedingSchedules->removeElement($feedingSchedule)) {
            // set the owning side to null (unless already changed)
            if ($feedingSchedule->getLifeStage() === $this) {
                $feedingSchedule->setLifeStage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Molt>
     */
    public function getMolts(): Collection
    {
        return $this->molts;
    }

    public function addMolt(Molt $molt): static
    {
        if (!$this->molts->contains($molt)) {
            $this->molts->add($molt);
            $molt->setLifeStage($this);
        }

        return $this;
    }

    public function removeMolt(Molt $molt): static
    {
        if ($this->molts->removeElement($molt)) {
            // set the owning side to null (unless already changed)
            if ($molt->getLifeStage() === $this) {
                $molt->setLifeStage(null);
            }
        }

        return $this;
    }
}
