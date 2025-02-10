<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\Admin\TablesCrudController;
use App\Repository\TablesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new Get(),
        new Get(
            uriTemplate: 'tableNum/{id}',
            controller: TablesCrudController::class,
            name: 'by_Number',
        ),
        new Get(
            uriTemplate: '/tables/{id}/guests',
            name: 'guests_in_table',
        ),
        new Get(
            uriTemplate: '/tables_stats',
            controller:TablesCrudController::class,
            name: 'tables_stats',
        ),
        new Post(security: "is_granted('ROLE_ADMIN')"),
    ],

)]

#[ORM\Entity(repositoryClass: TablesRepository::class)]
class Tables
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $max_people = null;

    /**
     * @var Collection<int, Guests>
     */
    #[ORM\OneToMany(targetEntity: Guests::class, mappedBy: 'tableIn')]
    private Collection $guests;

    public function __construct()
    {
        $this->guests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMaxPeople(): ?int
    {
        return $this->max_people;
    }

    public function setMaxPeople(int $max_people): static
    {
        $this->max_people = $max_people;

        return $this;
    }

    /**
     * @return Collection<int, Guests>
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function addGuest(Guests $guest): static
    {
        if (!$this->guests->contains($guest)) {
            $this->guests->add($guest);
            $guest->setTableIn($this);
        }

        return $this;
    }

    public function removeGuest(Guests $guest): static
    {
        if ($this->guests->removeElement($guest)) {
            // set the owning side to null (unless already changed)
            if ($guest->getTableIn() === $this) {
                $guest->setTableIn(null);
            }
        }

        return $this;
    }

    public function getGuestsInTable(): int
    {
        return count($this->guests);
    }


}
