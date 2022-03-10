<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReclamationRepository::class)
 */
class Reclamation
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
    private $contenu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat_reclamation;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="reclamation")
     */
    private $utilisateur;

<<<<<<< Updated upstream
=======
    /**
     * @ORM\OneToOne(targetEntity=ReclamationReponse::class, mappedBy="reclamation")
     * @JoinColumn(name="reclamation_Rep_id", referencedColumnName="id",onDelete="CASCADE")
     * @Groups("reclamations")
     */
    private $reclamationRep;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sujet;

>>>>>>> Stashed changes
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getEtatReclamation(): ?string
    {
        return $this->etat_reclamation;
    }

    public function setEtatReclamation(string $etat_reclamation): self
    {
        $this->etat_reclamation = $etat_reclamation;

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
<<<<<<< Updated upstream
=======

    public function getReclamationRep(): ?ReclamationReponse
    {
        return $this->reclamationRep;
    }

    public function setReclamationRep(?ReclamationReponse $reclamationRep): self
    {
        // set the owning side of the relation if necessary
        if ($reclamationRep->getReclamation() !== $this) {
            $reclamationRep->setReclamation($this);
        }

        $this->reclamationRep = $reclamationRep;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }
>>>>>>> Stashed changes
}
