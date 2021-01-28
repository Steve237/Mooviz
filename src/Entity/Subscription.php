<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    private static $planDataNames = ['free','starter','premium'];

    
    private static $planDataPrices = [

        'free' => 0, // 0$
        'starter' => 3, // 15$
        'premium' => 30, // 29$

    ];

   
    public static function getPlanDataNameByIndex(int $index): string
    {
        return self::$planDataNames[$index];
    }

   
    public static function getPlanDataPriceByName(string $name): int
    {
        return self::$planDataPrices[$name];
    }

    
    public static function getPlanDataNames(): array
    {
        return self::$planDataNames;
    }

   
    public static function getPlanDataPrices(): array
    {
        return self::$planDataPrices;
    }
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $plan;

    /**
     * @ORM\Column(type="datetime")
     */
    private $valid_to;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $payment_status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $free_plan_used;

    /**
     * @ORM\OneToOne(targetEntity=Users::class, mappedBy="subscription", cascade={"persist", "remove"})
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $verifpayment;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->valid_to;
    }

    public function setValidTo(\DateTimeInterface $valid_to): self
    {
        $this->valid_to = $valid_to;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus(?string $payment_status): self
    {
        $this->payment_status = $payment_status;

        return $this;
    }

    public function getFreePlanUsed(): ?bool
    {
        return $this->free_plan_used;
    }

    public function setFreePlanUsed(bool $free_plan_used): self
    {
        $this->free_plan_used = $free_plan_used;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        // set (or unset) the owning side of the relation if necessary
        $newSubscription = null === $users ? null : $this;
        if ($users->getSubscription() !== $newSubscription) {
            $users->setSubscription($newSubscription);
        }

        return $this;
    }

    public function getVerifpayment(): ?string
    {
        return $this->verifpayment;
    }

    public function setVerifpayment(?string $verifpayment): self
    {
        $this->verifpayment = $verifpayment;

        return $this;
    }


}
