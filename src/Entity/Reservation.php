<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $emailUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'La date de réservation est obligatoire.')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureDebut = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureFin = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $idStationDepart = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $idStationArrivee = null;

    #[ORM\Column(length: 10)]
    private ?string $typeVelo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getEmailUser(): ?string
    {
        return $this->emailUser;
    }

    public function setEmailUser(string $emailUser): static
    {
        $this->emailUser = $emailUser;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(\DateTimeInterface $heureDebut): static
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heureFin;
    }

    public function setHeureFin(\DateTimeInterface $heureFin): static
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    public function getIdStationDepart(): ?string
    {
        return $this->idStationDepart;
    }

    public function setIdStationDepart(string $idStationDepart): static
    {
        $this->idStationDepart = $idStationDepart;

        return $this;
    }

    public function getIdStationArrivee(): ?string
    {
        return $this->idStationArrivee;
    }

    public function setIdStationArrivee(string $idStationArrivee): static
    {
        $this->idStationArrivee = $idStationArrivee;

        return $this;
    }

    public function getTypeVelo(): ?string
    {
        return $this->typeVelo;
    }

    public function setTypeVelo(string $typeVelo): static
    {
        $this->typeVelo = $typeVelo;

        return $this;
    }
}
