<?php

namespace Infrastructure\Http\Validation;

use Infrastructure\Http\Response;

class StringRule
{
    public function validar($campo, $valor): bool
    {
        return is_string($valor);
    }

    public function message($campo): string
    {
        return Response::json([
            'message' => 'El campo ' . $campo . ' debe ser un string'
        ]);
    }
}
