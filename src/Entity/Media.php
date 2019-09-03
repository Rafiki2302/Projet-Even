<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Vich\Uploadable()
 * @package App\Entity
 */
class Media
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Participant", mappedBy="media")
     */
    private $participant;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $urlImg;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="participant_images", fileNameProperty="urlImg")
     *
     * @Assert\Image(
     *     maxWidth = 3000,
     *     maxHeight = 3000,
     *     maxWidthMessage="La taille de l'image ne peut pas dépasser {{ max_width }} x 3000 pixels",
     *     maxHeightMessage="La taille de l'image ne peut pas dépasser 3000 x {{ max_height }} pixels",
     *     maxSize="1999k",
     *     maxSizeMessage="L'image ne peut pas dépasser 2Mo",
     *     mimeTypes = {
     *          "image/png",
     *          "image/jpeg",
     *          "image/jpg",
     *          "image/gif",
     *      },
     *     mimeTypesMessage="Format d'image invalide, insérez une image de format jpeg, jpg, gif ou png",
     * )
     */
    private $imageFile;

    /**
     * @var \DateTime
     * champ nécessaire au bon fonctionnement de l'upload d'image
     * voir https://symfony.com/doc/master/bundles/EasyAdminBundle/integration/vichuploaderbundle.html
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @return mixed
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * @param mixed $participant
     */
    public function setParticipant($participant): void
    {
        $this->participant = $participant;
    }

    /**
     * @return string
     */
    public function getUrlImg(): ?string
    {
        return $this->urlImg;
    }

    /**
     * @param string $urlImg
     */
    public function setUrlImg(?string $urlImg): void
    {
        $this->urlImg = $urlImg;
    }

    /**
     * @return File
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File $imageFile
     */
    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
        //pour que l'upload fonctionne, il faut qu'au moins un des champs de la BDD soit modifié, ici updateAt
        //voir https://symfony.com/doc/master/bundles/EasyAdminBundle/integration/vichuploaderbundle.html
        if($imageFile !== null){
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


}