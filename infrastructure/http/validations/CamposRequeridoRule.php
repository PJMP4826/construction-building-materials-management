<?php

namespace Infrastructure\Http\Validation;

class CamposRequeridoRule
{

    public function validar($campo, $valor): bool
    {
        return !is_null($valor) && $valor !== '' && !is_null($campo) && $campo !== '';
    }

    public function message($campo): string
    {
        return "El campo {$campo} es obligatorio";
    }
}
