<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AbonnementsRepository;

/**
 * @ORM\Entity(repositoryClass=AbonnementsRepository::class)
 */
class Abonnements
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="abonnements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $abonne;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="abonnements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $abonnements;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbonne(): ?Users
    {
        return $this->abonne;
    }

    public function setAbonne(?Users $abonne): self
    {
        $this->abonne = $abonne;

        return $this;
    }

    public function getAbonnements(): ?Users
    {
        return $this->abonnements;
    }

    public function setAbonnements(?Users $abonnements): self
    {
        $this->abonnements = $abonnements;

        return $this;
    }
}
