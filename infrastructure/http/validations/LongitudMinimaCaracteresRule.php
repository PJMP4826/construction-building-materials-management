<?php

namespace Infrastructure\Http\Validation;

use Infrastructure\Http\Response;

class LongitudMinimaCaracteresRule
{

    protected int $min;

    public function __construct(int $min)
    {
        $this->min = $min;
    }

    public function validar($campo, $valor): bool
    {
        if (empty($valor)) {
            return true;
        }
        return strlen($valor) >= $this->min;
    }

    public function message($campo): string
    {
        return Response::json([
            'message' => 'El campo ' . $campo . ' debe tener al menos ' . $this->min . ' caracteres'
        ]);
    }
}
