<?php

namespace App\Entity;

use App\Repository\PerformanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerformanceRepository::class)]
class Performance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'performances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Joueur $joueur = null;

    #[ORM\ManyToOne(inversedBy: 'performances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Matchs $match = null;

    #[ORM\Column]
    private ?int $minutesJouees = 0;

    #[ORM\Column]
    private ?int $buts = 0;

    #[ORM\Column]
    private ?int $passesDecisives = 0;

    #[ORM\Column]
    private ?int $cartonJaune = 0;

    #[ORM\Column]
    private ?int $cartonRouge = 0;

    #[ORM\Column]
    private ?float $noteMatch = 0;

    public function getId(): ?int { return $this->id; }

    public function getJoueur(): ?Joueur { return $this->joueur; }
    public function setJoueur(Joueur $j): static { $this->joueur = $j; return $this; }

    public function getMatch(): ?Matchs { return $this->match; }
    public function setMatch(Matchs $m): static { $this->match = $m; return $this; }

    public function getMinutesJouees(): ?int { return $this->minutesJouees; }
    public function setMinutesJouees(int $m): static { $this->minutesJouees = $m; return $this; }

    public function getButs(): ?int { return $this->buts; }
    public function setButs(int $b): static { $this->buts = $b; return $this; }

    public function getPassesDecisives(): ?int { return $this->passesDecisives; }
    public function setPassesDecisives(int $p): static { $this->passesDecisives = $p; return $this; }

    public function getCartonJaune(): ?int { return $this->cartonJaune; }
    public function setCartonJaune(int $cj): static { $this->cartonJaune = $cj; return $this; }

    public function getCartonRouge(): ?int { return $this->cartonRouge; }
    public function setCartonRouge(int $cr): static { $this->cartonRouge = $cr; return $this; }

    public function getNoteMatch(): ?float { return $this->noteMatch; }
    public function setNoteMatch(float $n): static { $this->noteMatch = $n; return $this; }
}
