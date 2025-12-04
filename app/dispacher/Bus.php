<?php

namespace App\Dispacher;

class Bus
{
    private array $handlers = [];

    public function register(string $messageClass, object $handler)
    {
        $this->handlers[$messageClass] = $handler;
    }

    public function dispatch(object $message): mixed
    {
        $messageClass = get_class($message);

        if (!isset($this->handlers[$messageClass])) {
            throw new \RuntimeException(
                "No hay handler registrado para: {$messageClass}"
            );
        }

        $handler = $this->handlers[$messageClass];

        if (!method_exists($handler, 'handle')) {
            throw new \RuntimeException(
                "El handler para {$messageClass} no tiene método handle()"
            );
        }

        return $handler->handle($message);
    }

    // verifica si hay un handler registrado para un mensaje
    public function hasHandler(string $messageClass): bool
    {
        return isset($this->handlers[$messageClass]);
    }
}