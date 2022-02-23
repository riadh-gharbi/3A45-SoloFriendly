<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FactureRepository::class)
 * @ORM\Table(name="Facture")
 */
class Facture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice("En Cours","Effectué","Annulé")
     */
    private $statut;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date
     * @Assert\GreaterThanOrEqual("today")
     */
    private $date_creation;

    /**
     *
     * @ORM\Column(type="date", nullable=true)
     *  @Assert\Date
     * @Assert\GreaterThanOrEqual(propertyPath="dateCreation",
    message="La date de paiement doit être supérieur ou égale à la date de creation")
     */
    private $date_paiement;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice("En Ligne","Cash")
     */
    private $type_paiement;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="factures_proposee")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="factures_recu")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Planning::class, inversedBy="factures")
     */
    private $planning;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(?\DateTimeInterface $date_paiement): self
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getTypePaiement(): ?string
    {
        return $this->type_paiement;
    }

    public function setTypePaiement(?string $type_paiement): self
    {
        $this->type_paiement = $type_paiement;

        return $this;
    }

    public function getOwner(): ?Utilisateur
    {
        return $this->owner;
    }

    public function setOwner(?Utilisateur $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getClient(): ?Utilisateur
    {
        return $this->client;
    }

    public function setClient(?Utilisateur $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function setPlanning(?Planning $planning): self
    {
        $this->planning = $planning;

        return $this;
    }


}
