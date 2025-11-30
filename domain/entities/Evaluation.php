<?php

namespace Domain\Entities;

class Evaluation
{
    private int $id;
    private int $deliveryId;
    private int $transportistId;
    private float $onTimeRating;
    private \DateTime $create_at;

    /**
     * @param int $deliveryId
     * @param int $transportistId
     * @param int $onTimeRating
     */
    public function __construct(
        int $deliveryId,
        int $transportistId,
        int $onTimeRating
    )
    {
        $this->deliveryId = $deliveryId;
        $this->transportistId = $transportistId;
        $this->onTimeRating = $onTimeRating;
        $this->validate();
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function validate(): void
    {
        if ($this->deliveryId <= 0) {
            throw new \InvalidArgumentException("La entrega no se ha proporcionado");
        }

        if ($this->transportistId <= 0) {
            throw new \InvalidArgumentException("El transporte no se ha proporcionado");
        }

        if ($this->onTimeRating < 1 || $this->onTimeRating > 5) {
            throw new \InvalidArgumentException("La calificacion de puntualidad debe estar entre 1 y 5");
        }
    }

    public function isRatingExcelent(): bool
    {
        return $this->onTimeRating >= 4.5;
    }

    public function isRatingGood(): bool
    {
        return $this->onTimeRating >= 3.5 && $this->onTimeRating < 4.5;
    }

    public function isRatingRegular(): bool
    {
        return $this->onTimeRating >= 2.5 && $this->onTimeRating < 3.5;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDeliveryId(): int
    {
        return $this->deliveryId;
    }

    public function setDeliveryId(int $deliveryId): void
    {
        $this->deliveryId = $deliveryId;
    }

    public function getTransportistId(): int
    {
        return $this->transportistId;
    }

    public function setTransportistId(int $transportistId): void
    {
        $this->transportistId = $transportistId;
    }

    public function getOnTimeRating(): float
    {
        return $this->onTimeRating;
    }

    public function setOnTimeRating(float $onTimeRating): void
    {
        $this->onTimeRating = $onTimeRating;
    }

    public function getCreateAt(): \DateTime
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTime $create_at): void
    {
        $this->create_at = $create_at;
    }
}