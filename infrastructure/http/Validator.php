<?php

namespace Infrastructure\Http;

//Validaciones 
use Infrastructure\Http\Validation\CamposRequeridoRule;
use Infrastructure\Http\Validation\EmailRule;
use Infrastructure\Http\Validation\LongitudMaximaCaracteresRule;
use Infrastructure\Http\Validation\LongitudMinimaCaracteresRule;
use Infrastructure\Http\Validation\StringRule;
use Infrastructure\Http\Validation\NumericRule;
use Infrastructure\Http\Validation\ArrayRule;


class Validator
{

    protected array $data;
    protected array $rules;
    protected array $errores = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->validarBody();
    }

    public function validarBody()
    {
        //verificar si el body está vacío
        if (empty($this->data)) {
            $this->errores['body'] = ['El cuerpo de la petición está vacío'];
            return;
        }

        foreach ($this->rules as $campo => $rules) {
            $valor = $this->data[$campo] ?? null;
            $this->errores[$campo] = [];

            //verificar si el campo existe en los datos
            if (!array_key_exists($campo, $this->data)) {
                $this->errores[$campo][] = "El campo {$campo} es obligatorio";
                continue;
            }

            foreach ($rules as $rule) {
                if (is_string($rule)) {
                    $rule = $this->stringToRule($rule);
                }

                if (!$rule->validar($campo, $valor)) {
                    $this->errores[$campo][] = $rule->message($campo);
                }
            }

            if (empty($this->errores[$campo])) {
                unset($this->errores[$campo]);
            }
        }
    }

    public function stringToRule(string $rule)
    {
        $partes = explode(':', $rule);
        $nombre = strtolower($partes[0]);
        $parametros = $partes[1] ?? null;

        return match ($nombre) {
            'required' => new CamposRequeridoRule(),
            'email' => new EmailRule(),
            'min' => new LongitudMinimaCaracteresRule((int) $parametros),
            'max' => new LongitudMaximaCaracteresRule((int) $parametros),
            'string' => new StringRule(),
            'numeric' => new NumericRule,
            'array' => new ArrayRule,

            default => null
        };
    }

    public function fails(): bool
    {
        return !empty($this->errores);
    }

    public function errores(): array
    {
        return $this->errores;
    }

    public static function normalizeString(string $value): string
    {
        $value = trim($value);

        $value = preg_replace('/\s+/', ' ', $value);

        $value = mb_strtolower($value, 'UTF-8');

        //eliminar tildes
        $value = strtr($value, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'Á' => 'a',
            'É' => 'e',
            'Í' => 'i',
            'Ó' => 'o',
            'Ú' => 'u',
            'Ü' => 'u',
        ]);

        return $value;
    }
}
