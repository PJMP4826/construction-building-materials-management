<?php

namespace Infrastructure\Http;

use stdClass;

class Request
{
    public function input(bool $asociativos = true): array|stdClass
    {
        return json_decode(file_get_contents("php://input"), $asociativos ? $asociativos : null) ?? [];
    }

    public function query(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    public function hasPost(string $key): bool
    {
        return isset($_POST[$key]);
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function uri(): string
    {
        return strtok($_SERVER['REQUEST_URI'], '?');
    }

    public function file(string $key): array
    {
        if (!isset($_FILES[$key])) {
            return [$key => null];
        }

        //si es un archivo múltiple
        if (is_array($_FILES[$key]['name'])) {
            $files = [];
            $count = count($_FILES[$key]['name']);

            for ($i = 0; $i < $count; $i++) {
                if ($_FILES[$key]['error'][$i] === 0) {
                    $files[] = [
                        'name' => $_FILES[$key]['name'][$i],
                        'type' => $_FILES[$key]['type'][$i],
                        'tmp_name' => $_FILES[$key]['tmp_name'][$i],
                        'error' => $_FILES[$key]['error'][$i],
                        'size' => $_FILES[$key]['size'][$i]
                    ];
                }
            }
            return [$key => empty($files) ? null : $files];
        }

        //si es un archivo individual
        return [$key => ($_FILES[$key]['error'] === 0) ? $_FILES[$key] : null];
    }
}
