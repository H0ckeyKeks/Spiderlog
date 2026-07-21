<?php

namespace App\Entity;

use App\Repository\FoodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodRepository::class)]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'foods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FoodSpecies $foodSpecies = null;

    #[ORM\ManyToOne(inversedBy: 'foods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FoodSize $foodSize = null;

    /**
     * @var Collection<int, Feedings>
     */
    #[ORM\OneToMany(targetEntity: Feedings::class, mappedBy: 'food')]
    private Collection $feedings;

    public function __construct()
    {
        $this->feedings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFoodSpecies(): ?FoodSpecies
    {
        return $this->foodSpecies;
    }

    public function setFoodSpecies(?FoodSpecies $foodSpecies): static
    {
        $this->foodSpecies = $foodSpecies;

        return $this;
    }

    public function getFoodSize(): ?FoodSize
    {
        return $this->foodSize;
    }

    public function setFoodSize(?FoodSize $foodSize): static
    {
        $this->foodSize = $foodSize;

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
            $feeding->setFood($this);
        }

        return $this;
    }

    public function removeFeeding(Feedings $feeding): static
    {
        if ($this->feedings->removeElement($feeding)) {
            // set the owning side to null (unless already changed)
            if ($feeding->getFood() === $this) {
                $feeding->setFood(null);
            }
        }

        return $this;
    }
}
