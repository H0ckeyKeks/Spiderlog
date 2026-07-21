<?php

namespace App\Entity;

use App\Repository\SpiderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpiderRepository::class)]
class Spider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pictureLink = null;

    #[ORM\Column(length: 255)]
    private ?string $coloration = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateAquired = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateOfDeath = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $archivedAt = null;

    #[ORM\ManyToOne(inversedBy: 'spiders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Species $species = null;

    #[ORM\ManyToOne(inversedBy: 'spiders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LifeStage $initialLifeStage = null;

    /**
     * @var Collection<int, Molt>
     */
    #[ORM\OneToMany(targetEntity: Molt::class, mappedBy: 'spider')]
    private Collection $molts;

    /**
     * @var Collection<int, Feedings>
     */
    #[ORM\OneToMany(targetEntity: Feedings::class, mappedBy: 'spider')]
    private Collection $feedings;


    public function __construct()
    {
        $this->molts = new ArrayCollection();
        $this->feedings = new ArrayCollection();
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

    public function getPictureLink(): ?string
    {
        return $this->pictureLink;
    }

    public function setPictureLink(?string $pictureLink): static
    {
        $this->pictureLink = $pictureLink;

        return $this;
    }

    public function getColoration(): ?string
    {
        return $this->coloration;
    }

    public function setColoration(string $coloration): static
    {
        $this->coloration = $coloration;

        return $this;
    }

    public function getDateAquired(): ?\DateTimeImmutable
    {
        return $this->dateAquired;
    }

    public function setDateAquired(\DateTimeImmutable $dateAquired): static
    {
        $this->dateAquired = $dateAquired;

        return $this;
    }

    public function getDateOfDeath(): ?\DateTimeImmutable
    {
        return $this->dateOfDeath;
    }

    public function setDateOfDeath(?\DateTimeImmutable $dateOfDeath): static
    {
        $this->dateOfDeath = $dateOfDeath;

        return $this;
    }

    public function getArchivedAt(): ?\DateTimeImmutable
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?\DateTimeImmutable $archivedAt): static
    {
        $this->archivedAt = $archivedAt;

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

    public function getInitialLifeStage(): ?LifeStage
    {
        return $this->initialLifeStage;
    }

    public function setInitialLifeStage(?LifeStage $initialLifeStage): static
    {
        $this->initialLifeStage = $initialLifeStage;

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
            $molt->setSpider($this);
        }

        return $this;
    }

    public function removeMolt(Molt $molt): static
    {
        if ($this->molts->removeElement($molt)) {
            // set the owning side to null (unless already changed)
            if ($molt->getSpider() === $this) {
                $molt->setSpider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Feedings>
     */
    public function getFeedings(): Collection
    {
        return $this->feedings;
    }

    public function addFeeding(Feedings $feeding): static
    {
        if (!$this->feedings->contains($feeding)) {
            $this->feedings->add($feeding);
            $feeding->setSpider($this);
        }

        return $this;
    }

    public function removeFeeding(Feedings $feeding): static
    {
        if ($this->feedings->removeElement($feeding)) {
            // set the owning side to null (unless already changed)
            if ($feeding->getSpider() === $this) {
                $feeding->setSpider(null);
            }
        }

        return $this;
    }
}
