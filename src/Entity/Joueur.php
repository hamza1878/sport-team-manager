<?php

namespace App\Entity;

use App\Repository\JoueurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JoueurRepository::class)]
class Joueur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column(length: 20)]
    private ?string $position = null;

    #[ORM\Column(nullable: true)]
    private ?int $numeroMaillot = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\ManyToOne(inversedBy: 'joueurs')]
    private ?Equipe $equipe = null;

    #[ORM\OneToMany(mappedBy: 'joueur', targetEntity: Performance::class)]
    private Collection $performances;

    public function __construct()
    {
        $this->performances = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $p): static { $this->prenom = $p; return $this; }

    public function getAge(): ?int { return $this->age; }
    public function setAge(int $age): static { $this->age = $age; return $this; }

    public function getPosition(): ?string { return $this->position; }
    public function setPosition(string $pos): static { $this->position = $pos; return $this; }

    public function getNumeroMaillot(): ?int { return $this->numeroMaillot; }
    public function setNumeroMaillot(?int $num): static { $this->numeroMaillot = $num; return $this; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): static { $this->photo = $photo; return $this; }

    public function getEquipe(): ?Equipe { return $this->equipe; }
    public function setEquipe(?Equipe $e): static { $this->equipe = $e; return $this; }

    /** @return Collection<int, Performance> */
    public function getPerformances(): Collection { return $this->performances; }
}
