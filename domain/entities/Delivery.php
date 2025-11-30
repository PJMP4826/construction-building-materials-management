<?php

namespace Domain\Entities;

use InvalidArgumentException;

class Delivery
{
    private ?int $id;
    private int $requestId;
    private int $transportistId;
    private \DateTime $delivery_date;
    private \DateTime $required_date;
    private bool $onTime;
    private string $signReceiver;
    private \DateTime $created_at;

    public function __construct(
        int       $requestId,
        int       $transportistId,
        \DateTime $delivery_date,
        \DateTime $required_date,
        bool      $onTime = false,
        string    $singReceiver
    )
    {
        $this->requestId = $requestId;
        $this->transportistId = $transportistId;
        $this->delivery_date = $delivery_date;
        $this->required_date = $required_date;
        $this->onTime = $onTime;
        $this->signReceiver = $singReceiver;
        $this->created_at = new \DateTime();
        $this->validate();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validate(): void
    {
        if ($this->requestId <= 0) {
            throw new InvalidArgumentException("Debes referenciar la solicitud");
        }

        if ($this->transportistId <= 0) {
            throw new InvalidArgumentException("Debes referenciar el transportista");
        }

        if ($this->isOnTime()) {
            $this->onTime = true;
        }
    }

    private function isOnTime(): bool
    {
        return $this->delivery_date <= $this->required_date;
    }

    public function daysLate(): string
    {
        if ($this->delivery_date > $this->required_date) {
            return (int) $this->required_date
                ->diff($this->delivery_date)
                ->format('%a');
        }
        return 0;
    }

    public function daysAnticipated(): string
    {
        if ($this->delivery_date < $this->required_date) {
            return (int) $this->required_date
                ->diff($this->delivery_date)
                ->format('%a');
        }
        return 0;
    }

    public function hasSign(): bool
    {
        return !empty($this->signReceiver);
    }

    public function setSign(string $sign): void
    {
        $this->signReceiver = !empty($sign) ? $sign : "";
    }

    public function isCompleteDelivery(): bool
    {
        return !empty($this->signReceiver);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getRequestId(): int
    {
        return $this->requestId;
    }

    public function setRequestId(int $requestId): void
    {
        $this->requestId = $requestId;
    }

    public function getTransportistId(): int
    {
        return $this->transportistId;
    }

    public function setTransportistId(int $transportistId): void
    {
        $this->transportistId = $transportistId;
    }

    public function getDeliveryDate(): \DateTime
    {
        return $this->delivery_date;
    }

    public function setDeliveryDate(\DateTime $delivery_date): void
    {
        $this->delivery_date = $delivery_date;
    }

    public function getRequiredDate(): \DateTime
    {
        return $this->required_date;
    }

    public function setRequiredDate(\DateTime $required_date): void
    {
        $this->required_date = $required_date;
    }

    public function setOnTime(bool $onTime): void
    {
        $this->onTime = $onTime;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }
}