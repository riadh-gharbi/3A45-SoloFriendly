<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 */
class Utilisateur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $cin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="integer")
     */
    private $numTel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $MotDePasse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $langue;

    /**
     * @ORM\OneToOne(targetEntity=Profile::class, inversedBy="utilisateur", cascade={"persist", "remove"})
     */
    private $profile;

    /**
     * @ORM\OneToMany(targetEntity=Destination::class, mappedBy="utilisateur")
     */
    private $destination;

    /**
     * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="utilisateur")
     */
    private $evenement;

    /**
     * @ORM\OneToMany(targetEntity=Planning::class, mappedBy="utilisateur")
     */
    private $planning;

    /**
     * @ORM\OneToMany(targetEntity=Reclamation::class, mappedBy="utilisateur")
     */
    private $reclamation;

    /**
     * @ORM\OneToMany(targetEntity=Newsletter::class, mappedBy="utilisateur")
     */
    private $newsletter;

    /**
     * @ORM\OneToMany(targetEntity=Paiement::class, mappedBy="owner")
     */
    private $factures_proposee;

    /**
     * @ORM\OneToMany(targetEntity=Paiement::class, mappedBy="client")
     */
    private $factures_recu;

    public function __construct()
    {
        $this->destination = new ArrayCollection();
        $this->evenement = new ArrayCollection();
        $this->planning = new ArrayCollection();
        $this->reclamation = new ArrayCollection();
        $this->newsletter = new ArrayCollection();
        $this->factures_proposee = new ArrayCollection();
        $this->factures_recu = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->numTel;
    }

    public function setNumTel(int $numTel): self
    {
        $this->numTel = $numTel;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->MotDePasse;
    }

    public function setMotDePasse(string $MotDePasse): self
    {
        $this->MotDePasse = $MotDePasse;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(?string $langue): self
    {
        $this->langue = $langue;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Collection|Destination[]
     */
    public function getDestination(): Collection
    {
        return $this->destination;
    }

    public function addDestination(Destination $destination): self
    {
        if (!$this->destination->contains($destination)) {
            $this->destination[] = $destination;
            $destination->setUtilisateur($this);
        }

        return $this;
    }

    public function removeDestination(Destination $destination): self
    {
        if ($this->destination->removeElement($destination)) {
            // set the owning side to null (unless already changed)
            if ($destination->getUtilisateur() === $this) {
                $destination->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenement(): Collection
    {
        return $this->evenement;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenement->contains($evenement)) {
            $this->evenement[] = $evenement;
            $evenement->setUtilisateur($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenement->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getUtilisateur() === $this) {
                $evenement->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Planning[]
     */
    public function getPlanning(): Collection
    {
        return $this->planning;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->planning->contains($planning)) {
            $this->planning[] = $planning;
            $planning->setUtilisateur($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        if ($this->planning->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getUtilisateur() === $this) {
                $planning->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reclamation[]
     */
    public function getReclamation(): Collection
    {
        return $this->reclamation;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamation->contains($reclamation)) {
            $this->reclamation[] = $reclamation;
            $reclamation->setUtilisateur($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamation->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUtilisateur() === $this) {
                $reclamation->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Newsletter[]
     */
    public function getNewsletter(): Collection
    {
        return $this->newsletter;
    }

    public function addNewsletter(Newsletter $newsletter): self
    {
        if (!$this->newsletter->contains($newsletter)) {
            $this->newsletter[] = $newsletter;
            $newsletter->setUtilisateur($this);
        }

        return $this;
    }

    public function removeNewsletter(Newsletter $newsletter): self
    {
        if ($this->newsletter->removeElement($newsletter)) {
            // set the owning side to null (unless already changed)
            if ($newsletter->getUtilisateur() === $this) {
                $newsletter->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Paiement[]
     */
    public function getFacturesProposee(): Collection
    {
        return $this->factures_proposee;
    }

    public function addFacturesProposee(Paiement $facturesProposee): self
    {
        if (!$this->factures_proposee->contains($facturesProposee)) {
            $this->factures_proposee[] = $facturesProposee;
            $facturesProposee->setOwner($this);
        }

        return $this;
    }

    public function removeFacturesProposee(Paiement $facturesProposee): self
    {
        if ($this->factures_proposee->removeElement($facturesProposee)) {
            // set the owning side to null (unless already changed)
            if ($facturesProposee->getOwner() === $this) {
                $facturesProposee->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Paiement[]
     */
    public function getFacturesRecu(): Collection
    {
        return $this->factures_recu;
    }

    public function addFacturesRecu(Paiement $facturesRecu): self
    {
        if (!$this->factures_recu->contains($facturesRecu)) {
            $this->factures_recu[] = $facturesRecu;
            $facturesRecu->setClient($this);
        }

        return $this;
    }

    public function removeFacturesRecu(Paiement $facturesRecu): self
    {
        if ($this->factures_recu->removeElement($facturesRecu)) {
            // set the owning side to null (unless already changed)
            if ($facturesRecu->getClient() === $this) {
                $facturesRecu->setClient(null);
            }
        }

        return $this;
    }
}
