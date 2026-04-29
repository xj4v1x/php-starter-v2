<?php

/**
 * Genera una URL absoluta respetando el APP_BASE_PATH configurado en .env
 * Uso en vistas: url('/users')  →  /php-starter/public/users
 */
function url(string $path = ''): string
{
    $base = rtrim($_ENV['APP_BASE_PATH'] ?? '', '/');
    $path = '/' . ltrim($path, '/');
    return $base . $path;
}
