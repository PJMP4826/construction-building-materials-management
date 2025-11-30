<?php

namespace Domain\Entities;

use Exception;

class Solicitud
{
    private int $id;
    private int $materialId;
    private int $cantidad;
    private string $direccionEntrega;
    private string $fechaRequerida;
    private string $estado; //PENDIENTE, ASIGNADA, ENTREGADA
    private ?int $transportistaId = null;
    private ?string $fechaEntrega = null;

    public function __construct(
        int    $materialId,
        int    $cantidad,
        string $direccionEntrega,
        string $fechaRequerida,
        string $estado = 'PENDIENTE'
    )
    {
        $this->materialId = $materialId;
        $this->cantidad = $cantidad;
        $this->direccionEntrega = $direccionEntrega;
        $this->fechaRequerida = $fechaRequerida;
        $this->estado = $estado;
    }


    public function asignarTransportista(int $transportistaId): void
    {
        if ($this->estado !== 'PENDIENTE') {
            throw new Exception("Solo se pueden asignar solicitudes pendientes");
        }

        $this->transportistaId = $transportistaId;
        $this->estado = 'ASIGNADA';
    }

    public function confirmarEntrega(string $fecha): void
    {
        if ($this->estado !== 'ASIGNADA') {
            throw new Exception("Solo se pueden confirmar entregas asignadas");
        }

        $this->fechaEntrega = $fecha;
        $this->estado = 'ENTREGADA';
    }

    public function esValida(): bool
    {
        return $this->cantidad > 0
            && !empty($this->direccionEntrega)
            && strtotime($this->fechaRequerida) > time();
    }

    public function estaPuntual(): bool
    {
        if (!$this->fechaEntrega) return false;

        return strtotime($this->fechaEntrega) <= strtotime($this->fechaRequerida);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getMaterialId(): int
    {
        return $this->materialId;
    }

    public function setMaterialId(int $materialId): void
    {
        $this->materialId = $materialId;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): void
    {
        $this->cantidad = $cantidad;
    }

    public function getDireccionEntrega(): string
    {
        return $this->direccionEntrega;
    }

    public function setDireccionEntrega(string $direccionEntrega): void
    {
        $this->direccionEntrega = $direccionEntrega;
    }

    public function getFechaRequerida(): string
    {
        return $this->fechaRequerida;
    }

    public function setFechaRequerida(string $fechaRequerida): void
    {
        $this->fechaRequerida = $fechaRequerida;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function getTransportistaId(): ?int
    {
        return $this->transportistaId;
    }

    public function setTransportistaId(?int $transportistaId): void
    {
        $this->transportistaId = $transportistaId;
    }

    public function getFechaEntrega(): ?string
    {
        return $this->fechaEntrega;
    }

    public function setFechaEntrega(?string $fechaEntrega): void
    {
        $this->fechaEntrega = $fechaEntrega;
    }
}