<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\PluviometrieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PluviometrieRepository::class)]
#[ApiResource(
    operations: [new Get(), new GetCollection(), new Post()],
)]
#[ORM\HasLifecycleCallbacks] //config horodatage
class Pluviometrie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $pluvio_heure = null;

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

    public function getPluvioHeure(): ?float
    {
        return $this->pluvio_heure;
    }

    public function setPluvioHeure(float $pluvio_heure): static
    {
        $this->pluvio_heure = $pluvio_heure;

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
