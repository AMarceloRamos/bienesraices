<?php

function conectarDB() {
    $url = "postgresql://portafoliodb_hb96_user:LUd1w30XSO8bNfXvwb5TwFrflfRnka8K@dpg-cves1qfnoe9s73b9ullg-a.frankfurt-postgres.render.com/portafoliodb_hb96";
    
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
