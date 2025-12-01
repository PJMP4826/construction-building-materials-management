<?php

namespace Infrastructure\Http;

class Response
{
    public static function json(
        array $data,
        int $status = 200
    ) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
