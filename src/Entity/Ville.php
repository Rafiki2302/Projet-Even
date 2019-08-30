<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VilleRepository")
 */
class Ville
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     *
     * @Assert\NotBlank(message="Le champ doit être rempli")
     * @Assert\Length(max=80,maxMessage="Le nom de la ville ne peut pas faire plus de {{ limit }} caractères")
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank(message="Le champ doit être rempli")
     * @Assert\Range(
     *     min = 1000,
     *      max = 98890,
     *      minMessage = "Un code postal ne peut pas être inférieur à {{ limit }}",
     *      maxMessage = "Un code postal ne peut pas être supérieur à {{ limit }}"
     * )
     */
    private $codePostal;

    /**
     * @ORM\OneToMany (targetEntity = "Lieu", mappedBy= "ville")
     */
    private $lieus;

    public function __construct()
    {
        $this->lieus = new ArrayCollection();
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

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection|Lieu[]
     */
    public function getProducts(): Collection
    {
        return $this->lieus;
    }

}
