<?php

namespace App\Entity;

use Serializable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @UniqueEntity(fields={"username"}),
 * message="Cet utilisateur existe déjà"
 * )
 * @UniqueEntity(fields={"email"}),
 * message="Cette adresse email est déjà utilisée"
 * )
 */
class Users implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5,max=15,minMessage="Il faut minimum 5 caractères", maxMessage="Il faut maximum 15 caractères")
    */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="entrez une adresse email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5,max=20,minMessage="Il faut au moins 5 caractères",maxMessage="Il faut au maximum 20 caractères")
    */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5,max=20,minMessage="Il faut au moins 5 caractères", maxMessage="Il faut au maximum 10 caractères")
     * @Assert\EqualTo(propertyPath="password",message="les mots de passe ne correspondent pas")
     */
    private $confirmPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activation_token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reset_token;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $roles;


    /**
     * @ORM\OneToMany(targetEntity=Playlist::class, mappedBy="user", orphanRemoval=true)
     */
    private $playlists;

    /**
     * @ORM\OneToOne(targetEntity=Avatar::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $imageuser;

    /**
     * @ORM\OneToMany(targetEntity=Videos::class, mappedBy="username", orphanRemoval=true)
     */
    private $videos;

    /**
     * @ORM\OneToMany(targetEntity=Notifications::class, mappedBy="origin", orphanRemoval=true)
     */
    private $notifications;

     /**
     * @ORM\OneToMany(targetEntity=Notifications::class, mappedBy="destination", orphanRemoval=true)
     */
    private $destination;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customerid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $plan;

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $verifsubscription;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activated;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $payed;

    /**
     * @ORM\OneToMany(targetEntity=VideoLike::class, mappedBy="user", orphanRemoval=true)
     */
    private $videoLikes;


    public function __construct()
    {
        $this->playlists = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->videoLikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    public function eraseCredentials()
    {
        
    }
    public function getSalt()
    {
        
    }
    public function getRoles()
    {
        return [$this->roles];
    }

    public function getActivationToken(): ?string
    {
        return $this->activation_token;
    }

    public function setActivationToken(?string $activation_token): self
    {
        $this->activation_token = $activation_token;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

   
    public function setRoles(string $roles): self
    {
        if($roles === null) {

            $this->roles = "ROLE_USER";
        
        } else {

            $this->roles = $roles;
        }
        
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
            $playlist->setUser($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        if ($this->playlists->contains($playlist)) {
            $this->playlists->removeElement($playlist);
            // set the owning side to null (unless already changed)
            if ($playlist->getUser() === $this) {
                $playlist->setUser(null);
            }
        }

        return $this;
    }

    public function getImageuser(): ?Avatar
    {
        return $this->imageuser;
    }

    public function setImageuser(Avatar $imageuser): self
    {
        $this->imageuser = $imageuser;

        // set the owning side of the relation if necessary
        if ($imageuser->getUser() !== $this) {
            $imageuser->setUser($this);
        }

        return $this;
    }


    public function serialize() {

        return serialize(array(
        $this->id,
        $this->username,
        $this->password,
        ));
        
        }
        
        public function unserialize($serialized) {
        
        list (
        $this->id,
        $this->username,
        $this->password,
        ) = unserialize($serialized);
        }

        /**
         * @return Collection|Videos[]
         */
        public function getVideos(): Collection
        {
            return $this->videos;
        }

        public function addVideo(Videos $video): self
        {
            if (!$this->videos->contains($video)) {
                $this->videos[] = $video;
                $video->setUsername($this);
            }

            return $this;
        }

        public function removeVideo(Videos $video): self
        {
            if ($this->videos->contains($video)) {
                $this->videos->removeElement($video);
                // set the owning side to null (unless already changed)
                if ($video->getUsername() === $this) {
                    $video->setUsername(null);
                }
            }

            return $this;
        }
        
        /**
         * @return Collection|Notifications[]
         */
        public function getNotifications(): Collection
        {
            return $this->notifications;
        }

        public function addNotification(Notifications $notification): self
        {
            if (!$this->notifications->contains($notification)) {
                $this->notifications[] = $notification;
                $notification->setOrigin($this);
            }

            return $this;
        }

        public function removeNotification(Notifications $notification): self
        {
            if ($this->notifications->contains($notification)) {
                $this->notifications->removeElement($notification);
                // set the owning side to null (unless already changed)
                if ($notification->getOrigin() === $this) {
                    $notification->setOrigin(null);
                }
            }

            return $this;
        }


        /**
         * @return Collection|Notifications[]
         */
        public function getDestination(): Collection
        {
            return $this->destination;
        }

        public function addDestination(Notifications $destination): self
        {
            if (!$this->notifications->contains($destination)) {
                $this->notifications[] = $destination;
                $destination->setDestination($this);
            }

            return $this;
        }

        public function removeDestination(Notifications $destination): self
        {
            if ($this->notifications->contains($destination)) {
                $this->notifications->removeElement($destination);
                // set the owning side to null (unless already changed)
                if ($destination->getDestination() === $this) {
                    $destination->setDestination(null);
                }
            }

            return $this;
        }


        public function getCustomerid(): ?string
        {
            return $this->customerid;
        }

        public function setCustomerid(?string $customerid): self
        {
            $this->customerid = $customerid;

            return $this;
        }

        public function getPlan(): ?string
        {
            return $this->plan;
        }

        public function setPlan(string $plan): self
        {
            $this->plan = $plan;

            return $this;
        }

        public function getCreatedAt(): ?\DateTimeInterface
        {
            return $this->createdAt;
        }

        public function setCreatedAt(\DateTimeInterface $createdAt): self
        {
            $this->createdAt = $createdAt;

            return $this;
        }

        public function getVerifsubscription(): ?string
        {
            return $this->verifsubscription;
        }

        public function setVerifsubscription(?string $verifsubscription): self
        {
            $this->verifsubscription = $verifsubscription;

            return $this;
        }

        public function getActivated(): ?bool
        {
            return $this->activated;
        }

        public function setActivated(bool $activated): self
        {
            $this->activated = false;

            return $this;
        }

        public function getPayed(): ?bool
        {
            return $this->payed;
        }

        public function setPayed(?bool $payed): self
        {
            $this->payed = $payed;

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
                $videoLike->setUser($this);
            }

            return $this;
        }

        public function removeVideoLike(VideoLike $videoLike): self
        {
            if ($this->videoLikes->removeElement($videoLike)) {
                // set the owning side to null (unless already changed)
                if ($videoLike->getUser() === $this) {
                    $videoLike->setUser(null);
                }
            }

            return $this;
        }

    }
