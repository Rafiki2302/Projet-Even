<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="le champ nom ne peut pas être vide")
     * @Assert\Length(max=30,maxMessage="Le nom ne peut pas faire plus de {{ limit }} caractères")
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime(message="Format de date invalide")
     * @Assert\NotBlank(message="Le champ doit être rempli !")
     * @var string A "Y-m-d" formatted value
     * @Assert\GreaterThan("today", message="La date de début doit être supérieure à la date actuelle")
     *
     */
    private $datedebut;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Le champ doit être rempli !")
     * @Assert\GreaterThan(0,message="La durée de l'événement ne peut pas être négative")
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="Le champ doit être rempli !")
     * @Assert\DateTime(message="Format de date invalide")
     * @var string A "Y-m-d" formatted value
     * @Assert\GreaterThan("today", message="La date de clôture doit être supérieure à la date actuelle")
     * @Assert\Expression("value < this.getDatedebut()", message= "cette date doit être antérieure à celle de l'évènement")
     */
    private $datecloture;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="le champ nombre ne peut pas être vide")
     * @Assert\GreaterThan(0,message="Le nombre de participants ne peut pas être négatif")
     */
    private $nbinscriptionsmax;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     *
     * @Assert\Length(max=500,maxMessage="La description ne peut pas faire plus de {{ limit }} caractères")
     */
    private $descriptioninfos;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=500, nullable=true)
     *
     * @Assert\Length(max=500,maxMessage="Le motif ne peut pas faire plus de {{ limit }} caractères")

     */
    private $motifAnnul;

    /**
     * @ORM\ManyToOne (targetEntity = "Participant", inversedBy= "listSortieOrg")
     */
    private $organisateur;

    /**
     * @ORM\ManyToMany(targetEntity="Participant", inversedBy="listSortiesInsc")
     */
    private $participants;

    /**
     * @ORM\ManyToOne (targetEntity = "Lieu", inversedBy= "sorties", cascade={"persist"})
     * @Assert\NotBlank(message="Le champ doit être rempli !")
     * @Assert\Valid()
     */
    private $lieu;

    /**
     * @ORM\ManyToOne (targetEntity = "Etat", inversedBy= "sorties")
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="sorties")
     */
    private $site;

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

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(?\DateTimeInterface $datedebut): self
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * @param mixed $lieu
     */
    public function setLieu($lieu): void
    {
        $this->lieu = $lieu;
    }

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat): void
    {
        $this->etat = $etat;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDatecloture(): ?\DateTimeInterface
    {
        return $this->datecloture;
    }

    public function setDatecloture(?\DateTimeInterface $datecloture): self
    {
        $this->datecloture = $datecloture;

        return $this;
    }

    public function getNbinscriptionsmax(): ?int
    {
        return $this->nbinscriptionsmax;
    }

    public function setNbinscriptionsmax(int $nbinscriptionsmax): self
    {
        $this->nbinscriptionsmax = $nbinscriptionsmax;

        return $this;
    }

    public function getDescriptioninfos(): ?string
    {
        return $this->descriptioninfos;
    }

    public function setDescriptioninfos(?string $descriptioninfos): self
    {
        $this->descriptioninfos = $descriptioninfos;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrganisateur()
    {
        return $this->organisateur;
    }

    /**
     * @param mixed $organisateur
     */
    public function setOrganisateur($organisateur)
    {
        $this->organisateur = $organisateur;

    }

    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param mixed $participants
     */
    public function setParticipants($participants): void
    {
        $this->participants = $participants;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site): void
    {
        $this->site = $site;
    }

    /**
     * @return string
     */
    public function getMotifAnnul(): ?string
    {
        return $this->motifAnnul;
    }

    /**
     * @param string $motifAnnul
     */
    public function setMotifAnnul(?string $motifAnnul): void
    {
        $this->motifAnnul = $motifAnnul;
    }


}
