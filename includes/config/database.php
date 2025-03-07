<?php

function conectarDB(): ?PDO {
    $db_url = getenv('DATABASE_URL');  // Render asigna esta variable automáticamente

    if (!$db_url) {
        error_log("Error: No se encontró la variable de entorno DATABASE_URL.");
        return null;
    }

    // Convertir la URL de PostgreSQL si es necesario
    $db_url = str_replace("postgresql://", "pgsql://", $db_url);
    $url_parts = parse_url($db_url);

    if (!$url_parts || !isset($url_parts['host'], $url_parts['user'], $url_parts['pass'], $url_parts['path'])) {
        error_log("Error: No se pudo parsear correctamente DATABASE_URL.");
        return null;
    }

    $host = $url_parts['host'];
    $port = $url_parts['port'] ?? '5432';  // Puerto predeterminado
    $dbname = ltrim($url_parts['path'], '/');  // Eliminar la barra inicial
    $user = $url_parts['user'];
    $password = $url_parts['pass'];

    // Verificar si el nombre de la BD tiene un guion bajo final y corregirlo
    if (str_ends_with($dbname, '_')) {
        $dbname = rtrim($dbname, '_');
    }

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    try {
        $db = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        error_log("Conexión exitosa a la BD: $dbname en $host:$port");
        return $db;
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        return null;
    }
}
