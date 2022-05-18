<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Bafford\PasswordStrengthBundle\Validator\Constraints as BAssert;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="integer", nullable=true, unique=true)
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numTel;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $langue;

    /**
     * @ORM\OneToOne(targetEntity=Profile::class, mappedBy="utilisateur", cascade={"persist", "remove"})
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
     * @ORM\OneToMany(targetEntity=Facture::class, mappedBy="owner")
     */
    private $factures_proposee;

    /**
     * @ORM\OneToMany(targetEntity=Facture::class, mappedBy="client")
     */
    private $factures_recu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBlocked = false;

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

    /**
     * @see UserInterface
     */
    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getNom();
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(?int $cin): self
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

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->numTel;
    }

    public function setNumTel(?int $numTel): self
    {
        $this->numTel = $numTel;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): self
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
        // unset the owning side of the relation if necessary
        if ($profile === null && $this->profile !== null) {
            $this->profile->setUtilisateur(null);
        }

        // set the owning side of the relation if necessary
        if ($profile !== null && $profile->getUtilisateur() !== $this) {
            $profile->setUtilisateur($this);
        }

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
    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getIsBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

}
