<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 */
class Site
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     *@ORM\OneToMany(targetEntity="Participant", mappedBy="site")
     */
    private $listUsers;

    /**
     * Site constructor.
     */
    public function __construct()
    {
        $this->listUsers = new ArrayCollection();
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

    public function getListUsers()
    {
        return $this->listUsers;
    }

    public function setListUsers($listUsers): self
    {
        $this->listUsers = $listUsers;

        return $this;
    }
}
