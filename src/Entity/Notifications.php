<?php

namespace App\Entity;

use App\Repository\NotificationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationsRepository::class)
 */
class Notifications
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $origin;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="notifications")
     */
    private $destination;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrigin(): ?Users
    {
        return $this->origin;
    }

    public function setOrigin(?Users $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getDestination(): ?Users
    {
        return $this->destination;
    }

    public function setDestination(?Users $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
