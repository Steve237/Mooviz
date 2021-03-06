<?php

namespace App\Entity;

use App\Entity\Users;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\VideosRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;



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
     * @ORM\Column(type="text")
     */
    private $videodescription;

    /**
     * @ORM\Column(type="date")
     */
    private $publicationdate;

    /**
     * @ORM\Column(length=255)
     */
    private $videoimage;

    /**
    * @ORM\Column(length=255)
    * 
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
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $views;

    /**
     * @ORM\OneToMany(targetEntity=Playlist::class, mappedBy="video", orphanRemoval=true)
     */
    private $playlists;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="videos");
     * @ORM\JoinColumn(nullable=false)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $privacy;

    /**
     * @ORM\OneToMany(targetEntity=VideoLike::class, mappedBy="video", orphanRemoval=true)
     */
    private $videoLikes;


    public function __construct()
    {
    
        $this->publicationdate = new \DateTime('now');
        $this->playlists = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->videoLikes = new ArrayCollection();
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


    public function getVideoimage()
    {
        return $this->videoimage;
    }

    public function setVideoimage($videoimage)
    {
        $this->videoimage = $videoimage;

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
     * @return Collection|Playlist[]
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): self
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists[] = $playlist;
            $playlist->setVideo($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        if ($this->playlists->contains($playlist)) {
            $this->playlists->removeElement($playlist);
            // set the owning side to null (unless already changed)
            if ($playlist->getVideo() === $this) {
                $playlist->setVideo(null);
            }
        }

        return $this;
    }



    /**
     * Permet de savoir si un utilisateur a ajouté la vidéo à la playlist
     * @param Users $user
     * @return bool
     */
    public function isSelectedByUser(Users $user) : bool {

        foreach($this->playlists as $playlist) {

            if ($playlist->getUser() === $user) return true;

        }

        return false;


    }

    public function getUsername(): ?Users
    {
        return $this->username;
    }

    public function setUsername(?Users $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPrivacy(): ?string
    {
        return $this->privacy;
    }

    public function setPrivacy(?string $privacy): self
    {
        $this->privacy = $privacy;

        return $this;
    }

    /**
     * @return Collection|VideoLike[]
     */
    public function getVideoLikes(): Collection
    {
        return $this->videoLikes;
    }

    public function addVideoLike(VideoLike $videoLike): self
    {
        if (!$this->videoLikes->contains($videoLike)) {
            $this->videoLikes[] = $videoLike;
            $videoLike->setVideo($this);
        }

        return $this;
    }

    public function removeVideoLike(VideoLike $videoLike): self
    {
        if ($this->videoLikes->removeElement($videoLike)) {
            // set the owning side to null (unless already changed)
            if ($videoLike->getVideo() === $this) {
                $videoLike->setVideo(null);
            }
        }

        return $this;
    }

    
    /**
     * Permet de savoir si un utilisateur a liké
     * @param Users $user
     * @return bool
     */
    public function isLikedByUser(Users $user) : bool {

        foreach($this->videoLikes as $videoLike) {

            if ($videoLike->getUser() === $user) return true;

        }

        return false;
    }


}