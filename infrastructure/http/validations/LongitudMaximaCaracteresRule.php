<?php

namespace Infrastructure\Http\Validation;

class LongitudMaximaCaracteresRule
{
    protected int $max;

    public function __construct(int $max)
    {
        $this->max = $max;
    }

    public function validar($campo, $valor): bool
    {
        if (empty($valor)) {
            return true;
        }
        return strlen($valor) <= $this->max;
    }

    public function message($campo): string
    {
        return "El campo {$campo} no debe exceder {$this->max} caracteres";
    }
}
