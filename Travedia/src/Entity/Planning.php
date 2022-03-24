<?php

namespace App\Entity;

use App\Repository\PlanningRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=PlanningRepository::class)
 */
class Planning
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \Date
     * @ORM\Column(type="date")
     * @Assert\Date
     * @Assert\GreaterThanOrEqual("today")
     */
    private $date_depart;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date
     * @Assert\GreaterThanOrEqual(propertyPath="date_depart",
    message="La date du fin doit être supérieure à la date début")
     */
    private $date_fin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive(message="La Prix doit etre positive")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type_plan;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La description est necessaire")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="planning")
     */
    private $utilisateur;

    /**
     * @ORM\ManyToMany(targetEntity=Evenement::class, inversedBy="plannings")
     */
    private $evenements;

    /**
     * @ORM\ManyToMany(targetEntity=Destination::class, inversedBy="plannings")
     * @Assert\NotBlank(message="La destination est necessaire")
     */
    private $destinations;

    /**
     * @ORM\ManyToOne(targetEntity=Actualite::class, inversedBy="plannings")
     */
    private $actualite;

    /**
     * @ORM\OneToMany(targetEntity=Paiement::class, mappedBy="planning")
     */
    private $paiement;

    /**
     * @ORM\ManyToMany(targetEntity=Hotel::class, inversedBy="plannings")
     */
    private $hotels;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
        $this->destinations = new ArrayCollection();
        $this->factures = new ArrayCollection();
        $this->hotels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->date_depart;
    }

    public function setDateDepart(?\DateTimeInterface $date_depart): self
    {
        $this->date_depart = $date_depart;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getTypePlan(): ?string
    {
        return $this->type_plan;
    }

    public function setTypePlan(string $type_plan): self
    {
        $this->type_plan = $type_plan;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->addPlanning($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            $evenement->removePlanning($this);
        }

        return $this;
    }

    /**
     * @return Collection|Destination[]
     */
    public function getDestinations(): Collection
    {
        return $this->destinations;
    }

    public function addDestination(Destination $destination): self
    {
        if (!$this->destinations->contains($destination)) {
            $this->destinations[] = $destination;
            $destination->addPlanning($this);
        }

        return $this;
    }

    public function removeDestination(Destination $destination): self
    {
        if ($this->destinations->removeElement($destination)) {
            $destination->removePlanning($this);
        }

        return $this;
    }

    public function getActualite(): ?Actualite
    {
        return $this->actualite;
    }

    public function setActualite(?Actualite $actualite): self
    {
        $this->actualite = $actualite;

        return $this;
    }

    /**
     * @return Collection|Paiement[]
     */
    public function getPaiements(): Collection
    {
        return $this->paiement;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->factures->contains($paiement)) {
            $this->factures[] = $paiement;
            $paiement->setPlanning($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->factures->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getPlanning() === $this) {
                $paiement->setPlanning(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Hotel[]
     */
    public function getHotels(): Collection
    {
        return $this->hotels;
    }

    public function addHotel(Hotel $hotel): self
    {
        if (!$this->hotels->contains($hotel)) {
            $this->hotels[] = $hotel;
        }

        return $this;
    }

    public function removeHotel(Hotel $hotel): self
    {
        $this->hotels->removeElement($hotel);

        return $this;
    }
    public function __toString()
    {
    return(string) $this->prix;

    }
}
