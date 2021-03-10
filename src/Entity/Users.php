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
use App\Entity\Abonnements;


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
     * @ORM\OneToMany(targetEntity=VideoLike::class, mappedBy="user")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Playlist::class, mappedBy="user")
     */
    private $playlists;

    /**
     * @ORM\OneToOne(targetEntity=Avatar::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $imageuser;

    /**
     * @ORM\OneToMany(targetEntity=Videos::class, mappedBy="username")
     */
    private $videos;

     /**
     * @ORM\ManyToMany(targetEntity=Users::class, mappedBy="following")
     */
    private $followers;

     /**
     * @ORM\ManyToMany(targetEntity=Users::class, inversedBy="followers")
     * @ORM\JoinTable(name="following", 
     *      joinColumns={
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="following_user_id", referencedColumnName="id")
     *      }
     * )
     */
    private $following;

    /**
     * @ORM\OneToMany(targetEntity=Abonnements::class, mappedBy="abonne", orphanRemoval=true)
     */
    private $abonnements;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="username")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Notifications::class, mappedBy="origin")
     */
    private $notifications;

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
     * @ORM\ManyToMany(targetEntity=Likes::class, mappedBy="userid")
     */
    private $userlikes;

    /**
     * @ORM\OneToMany(targetEntity=Videodislike::class, mappedBy="user", orphanRemoval=true)
     */
    private $dislikes;


    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->playlists = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->abonnements = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->userlikes = new ArrayCollection();
        $this->dislikes = new ArrayCollection();

       
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
     * @return Collection|VideoLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(VideoLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(VideoLike $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
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
         * @return mixed
         */
        public function getFollowers(){


            return $this->followers;
        }

        /**
         * @return mixed
         */
        public function getFollowing(){


            return $this->following;
        }

        /**
         * @return Collection|Abonnements[]
         */
        public function getAbonnements(): Collection
        {
            return $this->abonnements;
        }

        public function addAbonnement(Abonnements $abonnement): self
        {
            if (!$this->abonnements->contains($abonnement)) {
                $this->abonnements[] = $abonnement;
                $abonnement->setAbonne($this);
            }

            return $this;
        }

        public function removeAbonnement(Abonnements $abonnement): self
        {
            if ($this->abonnements->contains($abonnement)) {
                $this->abonnements->removeElement($abonnement);
                // set the owning side to null (unless already changed)
                if ($abonnement->getAbonne() === $this) {
                    $abonnement->setAbonne(null);
                }
            }

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
                $comment->setUsername($this);
            }

            return $this;
        }

        public function removeComment(Comments $comment): self
        {
            if ($this->comments->contains($comment)) {
                $this->comments->removeElement($comment);
                // set the owning side to null (unless already changed)
                if ($comment->getUsername() === $this) {
                    $comment->setUsername(null);
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
         * @return Collection|Likes[]
         */
        public function getUserlikes(): Collection
        {
            return $this->userlikes;
        }

        public function addUserlike(Likes $userlike): self
        {
            if (!$this->userlikes->contains($userlike)) {
                $this->userlikes[] = $userlike;
                $userlike->addUserid($this);
            }

            return $this;
        }

        public function removeUserlike(Likes $userlike): self
        {
            if ($this->userlikes->removeElement($userlike)) {
                $userlike->removeUserid($this);
            }

            return $this;
        }

        /**
         * @return Collection|Videodislike[]
         */
        public function getDislikes(): Collection
        {
            return $this->dislikes;
        }

        public function addDislike(Videodislike $dislike): self
        {
            if (!$this->dislikes->contains($dislike)) {
                $this->dislikes[] = $dislike;
                $dislike->setUser($this);
            }

            return $this;
        }

        public function removeDislike(Videodislike $dislike): self
        {
            if ($this->dislikes->removeElement($dislike)) {
                // set the owning side to null (unless already changed)
                if ($dislike->getUser() === $this) {
                    $dislike->setUser(null);
                }
            }

            return $this;
        }
}
