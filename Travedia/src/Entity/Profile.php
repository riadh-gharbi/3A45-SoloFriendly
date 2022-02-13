<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 */
class Profile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $evaluation;

    /**
     * @ORM\OneToOne(targetEntity=Utilisateur::class, mappedBy="profile", cascade={"persist", "remove"})
     */
    private $utilisateur;

    /**
     * @ORM\OneToMany(targetEntity=Poste::class, mappedBy="profile", orphanRemoval=true)
     */
    private $postes;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="sender")
     */
    private $sent_messages;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="reciever")
     */
    private $recieved_messages;

    public function __construct()
    {
        $this->postes = new ArrayCollection();
        $this->sent_messages = new ArrayCollection();
        $this->recieved_messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

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

    public function getEvaluation(): ?int
    {
        return $this->evaluation;
    }

    public function setEvaluation(?int $evaluation): self
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        // unset the owning side of the relation if necessary
        if ($utilisateur === null && $this->utilisateur !== null) {
            $this->utilisateur->setProfile(null);
        }

        // set the owning side of the relation if necessary
        if ($utilisateur !== null && $utilisateur->getProfile() !== $this) {
            $utilisateur->setProfile($this);
        }

        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection|Poste[]
     */
    public function getPostes(): Collection
    {
        return $this->postes;
    }

    public function addPoste(Poste $poste): self
    {
        if (!$this->postes->contains($poste)) {
            $this->postes[] = $poste;
            $poste->setProfile($this);
        }

        return $this;
    }

    public function removePoste(Poste $poste): self
    {
        if ($this->postes->removeElement($poste)) {
            // set the owning side to null (unless already changed)
            if ($poste->getProfile() === $this) {
                $poste->setProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getSentMessages(): Collection
    {
        return $this->sent_messages;
    }

    public function addSentMessage(Message $sentMessage): self
    {
        if (!$this->sent_messages->contains($sentMessage)) {
            $this->sent_messages[] = $sentMessage;
            $sentMessage->setSender($this);
        }

        return $this;
    }

    public function removeSentMessage(Message $sentMessage): self
    {
        if ($this->sent_messages->removeElement($sentMessage)) {
            // set the owning side to null (unless already changed)
            if ($sentMessage->getSender() === $this) {
                $sentMessage->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getRecievedMessages(): Collection
    {
        return $this->recieved_messages;
    }

    public function addRecievedMessage(Message $recievedMessage): self
    {
        if (!$this->recieved_messages->contains($recievedMessage)) {
            $this->recieved_messages[] = $recievedMessage;
            $recievedMessage->setReciever($this);
        }

        return $this;
    }

    public function removeRecievedMessage(Message $recievedMessage): self
    {
        if ($this->recieved_messages->removeElement($recievedMessage)) {
            // set the owning side to null (unless already changed)
            if ($recievedMessage->getReciever() === $this) {
                $recievedMessage->setReciever(null);
            }
        }

        return $this;
    }
}
