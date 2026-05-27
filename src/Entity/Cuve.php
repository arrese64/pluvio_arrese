<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CuveRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CuveRepository::class)]
#[ApiResource]
#[ORM\HasLifecycleCallbacks] //config horodatage
class Cuve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $niveau_cm = null;

    #[ORM\Column(type: "datetime")] //config horodatage
    private ?\DateTime $horodatage = null;

    #[ORM\PrePersist]
    public function setHorodatageValue(): void
    {
        $this->horodatage = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNiveauCm(): ?float
    {
        return $this->niveau_cm;
    }

    public function setNiveauCm(float $niveau_cm): static
    {
        $this->niveau_cm = $niveau_cm;

        return $this;
    }

    public function getHorodatage(): ?\DateTime
    {
        return $this->horodatage;
    }

    public function setHorodatage(\DateTime $horodatage): static
    {
        $this->horodatage = $horodatage;

        return $this;
    }
}
