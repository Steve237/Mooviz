<?php

namespace App\Entity;

use App\Repository\LikesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LikesRepository::class)
 */
class Likes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Videos::class, inversedBy="videolikes")
     */
    private $videoid;

    /**
     * @ORM\ManyToMany(targetEntity=Users::class, inversedBy="userlikes")
     */
    private $userid;

    public function __construct()
    {
        $this->videoid = new ArrayCollection();
        $this->userid = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Videos[]
     */
    public function getVideoid(): Collection
    {
        return $this->videoid;
    }

    public function addVideoid(Videos $videoid): self
    {
        if (!$this->videoid->contains($videoid)) {
            $this->videoid[] = $videoid;
        }

        return $this;
    }

    public function removeVideoid(Videos $videoid): self
    {
        $this->videoid->removeElement($videoid);

        return $this;
    }

    /**
     * @return Collection|Users[]
     */
    public function getUserid(): Collection
    {
        return $this->userid;
    }

    public function addUserid(Users $userid): self
    {
        if (!$this->userid->contains($userid)) {
            $this->userid[] = $userid;
        }

        return $this;
    }

    public function removeUserid(Users $userid): self
    {
        $this->userid->removeElement($userid);

        return $this;
    }
}
