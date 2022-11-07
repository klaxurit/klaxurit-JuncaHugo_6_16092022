<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'trickGroup', targetEntity: Trick::class)]
    private Collection $trick;

    public function __construct()
    {
        $this->tricks = new ArrayCollection();
        $this->trick = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Trick>
     */
    public function getTrick(): Collection
    {
        return $this->trick;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->trick->contains($trick)) {
            $this->trick->add($trick);
            $trick->setTrickGroup($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->trick->removeElement($trick)) {
            // set the owning side to null (unless already changed)
            if ($trick->getTrickGroup() === $this) {
                $trick->setTrickGroup(null);
            }
        }

        return $this;
    }
}
