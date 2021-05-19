<?php

namespace App\Entity;

;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\VideobackgroundRepository;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=VideobackgroundRepository::class)
 */
class Videobackground
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Votre titre doit contenir au moins 2 caractères",
     *      maxMessage = "Votre titre doit contenir au moins 35 caractères"
     * )
     */
    private $videotitle;

    /**
     * @ORM\Column(type="date")
     */
    private $parutiondate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $videoduration;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(
     *      maxSize = "1000M",
     *      mimeTypes = {"video/mp4"},
     *      mimeTypesMessage = "Nous acceptons uniquement les vidéos au format mp4."
     * )
     */
    private $videolink;

     /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="videos")
     */
    private $category;

    /**
     * @ORM\Column(type="text")
     */
    private $videodescription;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVideotitle(): ?string
    {
        return $this->videotitle;
    }

    public function setVideotitle(string $videotitle): self
    {
        $this->videotitle = $videotitle;

        return $this;
    }

    public function getParutiondate(): ?\DateTimeInterface
    {
        return $this->parutiondate;
    }

    public function setParutiondate(\DateTimeInterface $parutiondate): self
    {
        $this->parutiondate = $parutiondate;

        return $this;
    }

    public function getVideoduration(): ?\DateTimeInterface
    {
        return $this->videoduration;
    }

    public function setVideoduration(\DateTimeInterface $videoduration): self
    {
        $this->videoduration = $videoduration;

        return $this;
    }

    public function getVideolink()
    {
        return $this->videolink;
    }

    public function setVideolink($videolink)
    {
        $this->videolink = $videolink;

        return $this;
    }

    
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getVideodescription(): ?string
    {
        return $this->videodescription;
    }

    public function setVideodescription(string $videodescription): self
    {
        $this->videodescription = $videodescription;

        return $this;
    }
}
