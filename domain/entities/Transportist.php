<?php

namespace Domain\Entities;

class Transportist
{
    private int $id;
    private string $name;
    private ?string $email;
    private string $deliveryArea;
    private bool $available;
    private float $ratingAverage;
    private int $deliveryCounts;
    private \DateTime $created_at;
    private \DateTime $update_at;

    /**
     * @param int $id
     * @param string $name
     * @param string $email
     * @param string $deliveryArea
     * @param float $ratingAverage
     * @param int $deliveryCounts
     * @param \DateTime $created_at
     * @param \DateTime $update_at
     */
    public function __construct(
        string $name,
        string $email,
        string $deliveryArea,
        bool   $available = true,
        float  $ratingAverage = 0.00,
        int    $deliveryCounts = 0
    )
    {
        $this->name = $name;
        $this->email = $email;
        $this->deliveryArea = $deliveryArea;
        $this->available = $available;
        $this->ratingAverage = $ratingAverage;
        $this->deliveryCounts = $deliveryCounts;
        $this->created_at = new \DateTime();
        $this->update_at = new \DateTime();
    }

    private function validate(): void
    {

        if (empty(trim($this->name))) {
            throw new \InvalidArgumentException("El nombre del material no puede ser vacío");
        }

        if (empty($this->deliveryArea)) {
            throw new \InvalidArgumentException("El la zona de cobertura no puede ser vacío");
        }

        if (!$this->validateEmail($this->email)) {
            throw new \InvalidArgumentException("El email es inválido");
        }
    }

    private function validateEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    public function markAsBusy(): void
    {
        $this->available = false;
        $this->update_at = new \DateTime();
    }

    public function markAsAvailable(): void
    {
        $this->available = true;
        $this->update_at = new \DateTime();
    }

    public function hasAvailable(): bool
    {
        if (!$this->available) {
            return false;
        }
        return true;
    }

    public function mayCoverArea(string $zone): bool
    {
        return strtolower($this->deliveryArea) === strtolower($zone);
    }

    public function updateRating(string $rating)
    {
        if ($rating > 0 && $rating < 5) {
            $this->ratingAverage = $rating;
        }

        $this->update_at = new \DateTime();
    }

    public function hasGoodRating()
    {
        return $this->ratingAverage >= 4;
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getDeliveryArea(): string
    {
        return $this->deliveryArea;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function getRatingAverage(): float
    {
        return $this->ratingAverage;
    }

    public function getDeliveryCounts(): int
    {
        return $this->deliveryCounts;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function getUpdateAt(): \DateTime
    {
        return $this->update_at;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setRatingAverage(float $ratingAverage): void
    {
        $this->ratingAverage = $ratingAverage;
    }

    public function setDeliveryCounts(int $deliveryCounts): void
    {
        $this->deliveryCounts = $deliveryCounts;
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUpdateAt(\DateTime $update_at): void
    {
        $this->update_at = $update_at;
    }
}