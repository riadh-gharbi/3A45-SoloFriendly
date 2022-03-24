<?php

namespace App\Entity;

use App\Repository\ReclamationReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ReclamationReponseRepository::class)
 */
class ReclamationReponse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ("reclamationReponse")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ("reclamationReponse")
     */
    private $contenu;

    /**
     * @ORM\OneToOne(targetEntity=Reclamation::class, inversedBy="reclamationRep", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)
     * @Groups ("reclamationReponse")
     */
    private $reclamation;

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

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(Reclamation $reclamation): self
    {
        $this->reclamation = $reclamation;

        return $this;
    }
}
