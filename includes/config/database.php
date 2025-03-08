<?php

function conectarDB() {
    $url = "postgresql://contactodb_i7hi_user:1BesM5VW4iwl5rdJP9IXqNwETiKW9hA0@dpg-cur1vpdds78s7384bkr0-a.oregon-postgres.render.com/contactodb_i7hi";
    
    // Parsear la URL para obtener los valores
    $dbopts = parse_url($url);

    $host = $dbopts["host"];
    $port = isset($dbopts["port"]) ? $dbopts["port"] : "5432"; // Puerto 5432 por defecto
    $user = $dbopts["user"];
    $pass = $dbopts["pass"];
    $dbname = ltrim($dbopts["path"], "/");

    try {
        $db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $db;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
