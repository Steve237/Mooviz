<?php

namespace App\Entity;

use App\Repository\VideobackgroundRepository;
use Doctrine\ORM\Mapping as ORM;

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
     */
    private $videotitle;

    /**
     * @ORM\Column(type="date")
     */
    private $parutiondate;

    /**
     * @ORM\Column(type="string")
     */
    private $videoduration;

    /**
     * @ORM\Column(type="string", length=255)
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

    public function getVideoduration()
    {
        return $this->videoduration;
    }

    public function setVideoduration($videoduration)
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
