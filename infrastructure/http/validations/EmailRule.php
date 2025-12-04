<?php

namespace Infrastructure\Http\Validation;

use Infrastructure\Http\Response;

class EmailRule
{
    public function validar($campo, $valor): bool
    {
        return filter_var($valor, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function message($campo): string
    {
        return Response::json([
            'message' => 'El campo ' . $campo . ' debe ser un correro válido'
        ]);
    }
}
