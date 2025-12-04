<?php

namespace Domain\Entities;

use Brick\Math\BigDecimal;
use Domain\Emuns\Unit;

class Material
{
    private ?string $id = null;
    private string $name;
    private ?string $description = null;
    private Unit $unit;
    private BigDecimal $unit_price;
    private int $stock;
    private bool $active;
    private \DateTime $created_at;
    private \DateTime $update_at;

    public function __construct(
        string     $name,
        ?string    $description = null,
        Unit       $unit,
        BigDecimal $unit_price,
        int        $stock = 0,
        bool       $active = true
    )
    {
        $this->name = $name;
        $this->description = $description;
        $this->unit = $unit;
        $this->unit_price = $unit_price;
        $this->stock = $stock;
        $this->active = $active;
        $this->created_at = new \DateTime();
        $this->update_at = new \DateTime();
        $this->validate();
    }

    private function validate(): void
    {

        if (empty(trim($this->name))) {
            throw new \InvalidArgumentException("El nombre del material no puede ser vacío");
        }

        if ($this->unit_price->isNegative()) {
            throw new \InvalidArgumentException("El precio unitario no puede ser negativo");
        }

        if ($this->stock < 0) {
            throw new \InvalidArgumentException("El stock actual no puede ser negativo");
        }
    }

    public function updateStock(int $amount): void
    {
        $newStock = $this->stock + $amount;

        if ($newStock < 0) {
            throw new \DomainException(
                "El stock actual es negativo"
            );
        }

        $this->stock = $newStock;

        $this->update_at = new \DateTime();
    }

    public function reduceStock(int $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException(
                "La cantidad debe ser positiva"
            );
        }
        if ($this->stock < $amount) {
            throw new \InvalidArgumentException(
                "No hay suficiente stock"
            );
        }

        $this->update_at = new \DateTime();
        $this->updateStock(-$amount);
    }

    public function upgradeStock(int $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("La cantidad a aumentar debe ser positiva");
        }

        $this->updateStock($amount);
    }

    public function updatePrice(BigDecimal $newAmount): void
    {
        if ($newAmount->compareTo(BigDecimal::zero()) < 0) {
            throw new \InvalidArgumentException(
                "El precio no puede ser negativo"
            );
        }

        $this->unit_price = $newAmount;
        $this->update_at = new \DateTime();
    }

    public function deactivate(): void
    {
        $this->active = false;
        $this->update_at = new \DateTime();
    }

    public function activate(): void
    {
        $this->active = true;
        $this->update_at = new \DateTime();
    }

    public function hasStockAvailable(int $requiredAmount): bool
    {
        return $this->stock >= $requiredAmount;
    }

    public function calculateTotal(int $amount): BigDecimal
    {
        return $this->unit_price->multipliedBy($amount);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getUnit(): Unit
    {
        return $this->unit;
    }

    public function getUnitPrice(): BigDecimal
    {
        return $this->unit_price;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function getUpdateAt(): \DateTime
    {
        return $this->update_at;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUpdateAt(\DateTime $update_at): void
    {
        $this->update_at = $update_at;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'unit' => $this->unit->value,
            'unit_price' => $this->unit_price->toFloat(),
            'stock' => $this->stock,
            'active' => $this->active

        ];
    }
}