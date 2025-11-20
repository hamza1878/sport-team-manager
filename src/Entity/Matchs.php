<?php

namespace App\Entity;

use App\Repository\MatchsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchsRepository::class)]
class Matchs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateMatch = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $heure = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $stade = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipeDomicile = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipeExterieur = null;

    #[ORM\Column]
    private ?int $scoreDomicile = 0;

    #[ORM\Column]
    private ?int $scoreExterieur = 0;

    #[ORM\OneToMany(mappedBy: 'match', targetEntity: Performance::class)]
    private Collection $performances;

    public function __construct()
    {
        $this->performances = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getDateMatch(): ?\DateTimeInterface { return $this->dateMatch; }
    public function setDateMatch(\DateTimeInterface $d): static { $this->dateMatch = $d; return $this; }

    public function getHeure(): ?\DateTimeInterface { return $this->heure; }
    public function setHeure(\DateTimeInterface $h): static { $this->heure = $h; return $this; }

    public function getStade(): ?string { return $this->stade; }
    public function setStade(?string $s): static { $this->stade = $s; return $this; }

    public function getEquipeDomicile(): ?Equipe { return $this->equipeDomicile; }
    public function setEquipeDomicile(Equipe $e): static { $this->equipeDomicile = $e; return $this; }

    public function getEquipeExterieur(): ?Equipe { return $this->equipeExterieur; }
    public function setEquipeExterieur(Equipe $e): static { $this->equipeExterieur = $e; return $this; }

    public function getScoreDomicile(): ?int { return $this->scoreDomicile; }
    public function setScoreDomicile(int $s): static { $this->scoreDomicile = $s; return $this; }

    public function getScoreExterieur(): ?int { return $this->scoreExterieur; }
    public function setScoreExterieur(int $s): static { $this->scoreExterieur = $s; return $this; }

    /** @return Collection<int, Performance> */
    public function getPerformances(): Collection { return $this->performances; }
}
