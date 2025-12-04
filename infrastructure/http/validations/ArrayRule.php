<?php

namespace Infrastructure\Http\Validation;

use Infrastructure\Http\Response;

class ArrayRule
{
    public function validar($campo, $valor): bool
    {
        return is_array($valor);
    }

    public function message($campo): string
    {
        return Response::json([
            'message' => 'El campo ' . $campo . ' debe ser un array'
        ]);
    }
}
