<?php

namespace App\Entity;

use App\Repository\DestinationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert ;
use Symfony\Component\Serializer\Annotation\Groups;



/**
 * @ORM\Entity(repositoryClass=DestinationRepository::class)
 */
class Destination
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ("destinations")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="le nom ne peut pas etre vide ")
     * @Assert\Length(max=100)
     * @Groups ("destinations")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="la description ne peut pas etre vide ")
     * @Assert\Length(max=250)
     * @Groups ("destinations")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups ("destinations")
     */
    private $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups ("destinations")
     */
    private $evaluation;

    // /**
    //  * @ORM\Column(type="string", length=255)
    //  */
    // private $region;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="destination")
     * @Groups ("destinations")
     */
    private $utilisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Evenement::class, inversedBy="destination")
     * @Groups ("destinations")
     */
    private $evenement;

    /**
     * @ORM\ManyToMany(targetEntity=Planning::class, inversedBy="destinations")
     * @Groups ("destinations")
     */
    private $planning;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="destination")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups ("destinations")
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups ("destinations")
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups ("destinations")
     */
    private $latitude;

   
    public function __construct()
    {
        $this->planning = new ArrayCollection();
       // $this->region = new ArrayCollection();

    }
   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

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

    public function getImage()
    {
        return $this->image;
    }

    public function setImage( $image): self
    {
        $this->image = $image;

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
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): self
    {
        $this->evenement = $evenement;

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
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        $this->planning->removeElement($planning);

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }
}
