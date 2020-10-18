<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\VideosRepository;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @ORM\Entity(repositoryClass=VideosRepository::class)
 * @Vich\Uploadable
 */
class Videos
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $videotitle;

    /**
     * @ORM\Column(type="string")
     */
    private $videodescription;

    /**
     * @ORM\Column(type="date")
     */
    private $publicationdate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $videoimage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $videolink;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="videos")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sliderimage;

    /**
     * @Vich\UploadableField(mapping="video_image", fileNameProperty="videoimage")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $views;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="video", orphanRemoval=true)
     */
    private $comments;

   public function __construct()
    {
    
        $this->publicationdate = new \DateTime('now');
        $this->comments = new ArrayCollection();

    }

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

    public function getVideodescription(): ?string
    {
        return $this->videodescription;
    }

    public function setVideodescription(string $videodescription): self
    {
        $this->videodescription = $videodescription;

        return $this;
    }

    public function getPublicationdate(): ?\DateTimeInterface
    {
        return $this->publicationdate;
    }

    public function setPublicationdate(\DateTimeInterface $publicationdate): self
    {
        $this->publicationdate = $publicationdate;

        return $this;
    }


    public function getVideoimage(): ?string
    {
        return $this->videoimage;
    }

    public function setVideoimage(?string $videoimage): self
    {
        $this->videoimage = $videoimage;

        return $this;
    }

    public function getVideolink(): ?string
    {
        return $this->videolink;
    }

    public function setVideolink(string $videolink): self
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

    public function getSliderimage(): ?string
    {
        return $this->sliderimage;
    }

    public function setSliderimage(?string $sliderimage): self
    {
        $this->sliderimage = $sliderimage;

        return $this;
    }

    
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;
        return $this;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getViews(): ?string
    {
        return $this->views;
    }

    public function setViews(?string $views): self
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setVideo($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getVideo() === $this) {
                $comment->setVideo(null);
            }
        }

        return $this;
    }

}
