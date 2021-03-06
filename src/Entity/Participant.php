<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 *
 * @Vich\Uploadable()
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     *
     * @Assert\NotBlank(message="Le champ doit être rempli")
     * @Assert\Length(max=30,maxMessage="Le nom ne peut pas faire plus de {{ limit }} caractères")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=30)
     *
     * @Assert\NotBlank(message="Le champ doit être rempli")
     * @Assert\Length(max=30,maxMessage="Le nom ne peut pas faire plus de {{ limit }} caractères")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     *
     * @Assert\NotBlank(message="Le champ doit être rempli")
     * @Assert\Length(max=30,maxMessage="Le nom ne peut pas faire plus de {{ limit }} caractères")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     *
     * @Assert\NotBlank(message="Le champ doit être rempli")
     * @Assert\Regex("#^0[1-9][0-9]{8}$#", message="Numéro de téléphone incorrect, ressaisissez")
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     *
     * @Assert\NotBlank(message="Le champ doit être rempli")
     * @Assert\Email(message="{{ value }} n'est pas un format d'email valide, ressaisissez")
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $motDePasse;

    /**
     * @ORM\Column(type="boolean")
     */
    private $admin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @var array
     *
     * @ORM\Column(type="array", length=20)
     */
    private $roles;

    /**
     * @ORM\ManyToOne(targetEntity="Media", inversedBy="participant", cascade={"persist"})
     *
     * @Assert\Valid()
     */
    private $media;

    /**
     * @Assert\NotBlank(message="Le champ doit être rempli !")
     *
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="listUsers")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;

    /**
     * @ORM\OneToMany (targetEntity = "Sortie", mappedBy= "organisateur")
     */
    private $listSortieOrg;

    /**
     * @ORM\ManyToMany(targetEntity="Sortie", mappedBy="participants")
     */
    private $listSortiesInsc;

    /**
     * Participant constructor.
     */
    public function __construct()
    {
        $this->roles = ["ROLE_USER"];
        $this->setActif(true);
        $this->setAdmin(false);
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(?string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(?bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setSite($site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getListSortieOrg()
    {
        return $this->listSortieOrg;
    }

    public function setListSortieOrg($listSortieOrg): self
    {
        $this->listSortieOrg = $listSortieOrg;

        return $this;
    }

    public function getListSortiesInsc()
    {
        return $this->listSortiesInsc;
    }

    public function setListSortiesInsc($listSortiesInsc): self
    {
        $this->listSortiesInsc = $listSortiesInsc;

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->motDePasse;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->pseudo;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return mixed
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param mixed $media
     */
    public function setMedia($media): void
    {
        $this->media = $media;
    }


}
