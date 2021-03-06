<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LieuRepository")
 */
class Lieu
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank(message="Le champ doit être rempli")
     * @Assert\Length(max=50,maxMessage="Le nom ne peut pas faire plus de {{ limit }} caractères")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     *
     * @Assert\Length(max=100,maxMessage="La rue ne peut pas faire plus de {{ limit }} caractères")
     */
    private $rue;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @Assert\Range(
     *     min = -90,
     *      max = 90,
     *      minMessage = "La latitude ne pas être inférieure à {{ limit }}",
     *     maxMessage = "La latitude ne pas être supérieure à {{ limit }}"
     * )
     *
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @Assert\Range(
     *     min = -180,
     *      max = 180,
     *      minMessage = "La longitude ne pas être inférieure à {{ limit }}",
     *      maxMessage = "La longitude ne pas être supérieure à {{ limit }}"
     * )
     */
    private $longitude;

    /**
     * @ORM\ManyToOne (targetEntity = "Ville", inversedBy= "lieus")
     *
     * @Assert\NotBlank(message="Le champ doit être rempli !")
     */
    private $ville;

    /**
     * @ORM\OneToMany (targetEntity = "Sortie", mappedBy= "lieu")
     */
    private $sorties;


    public function __construct()
    {
        $this->sorties = new ArrayCollection();
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

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(?string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }
    /**
     * @return mixed
     */
    public function getSorties()
    {
        return $this->sorties;
    }

    /**
     * @param mixed $sorties
     */
    public function setSorties($sorties): void
    {
        $this->sorties = $sorties;
    }

}
