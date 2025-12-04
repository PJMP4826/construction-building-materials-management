<?php

namespace Infrastructure\Http\Validation;

use Infrastructure\Http\Response;

class NumericRule
{
    public function validar($campo, $valor): bool
    {
        return is_numeric($valor);
    }

    public function message($campo): string
    {
        return Response::json([
            'message' => 'El campo ' . $campo . ' debe ser un numero'
        ]);
    }
}
