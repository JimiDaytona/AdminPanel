<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Admin\GuestsCrudController;
use App\Repository\GuestsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations: [
        new Get(),
        new Patch(),
        new Get(
            uriTemplate: '/guests',
            controller: GuestsCrudController::class,
            name: 'guests',
        )
    ],
)]

#[ORM\Entity(repositoryClass: GuestsRepository::class)]

class Guests
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $presense = null;

    #[ORM\ManyToOne(inversedBy: 'guests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tables $tableIn = null;

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

    public function isPresense(): ?bool
    {
        return $this->presense;
    }

    public function setPresense(bool $presense): static
    {
        $this->presense = $presense;

        return $this;
    }

    public function getTableIn(): ?Tables
    {
        return $this->tableIn;
    }

    public function setTableIn(?Tables $tableIn): static
    {
        $this->tableIn = $tableIn;

        return $this;
    }
}
