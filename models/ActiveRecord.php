<?php

namespace Model;

class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Errores
    protected static $errores = [];

    // Definir la conexión a la BD con PDO
    public static function setDB($database) {
        self::$db = $database;
    }

    // Validación
    public static function getErrores() {
        return static::$errores;
    }

    public function validar() {
        static::$errores = [];
        return static::$errores;
    }

    // Registros - CRUD
    public function guardar() {
        $resultado = '';
        if(!is_null($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }

    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;

        $resultado = self::consultarSQL($query);

        return $resultado;
    }

    // Busca un registro por su id
    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE id = :id";

        $resultado = self::consultarSQL($query, ['id' => $id]);

        return array_shift($resultado);
    }

    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT :limite";

        $resultado = self::consultarSQL($query, ['limite' => $limite]);

        return $resultado;
    }

    // crea un nuevo registro
    public function crear() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Preparar la consulta para insertar
        $query = "INSERT INTO " . static::$tabla . " (" . join(', ', array_keys($atributos)) . ") VALUES (:" . join(', :', array_keys($atributos)) . ")";
        
        // Ejecutar la consulta
        $stmt = self::$db->prepare($query);
        
        // Ejecutar con los atributos sanitizados
        return $stmt->execute($atributos);
    }

   public function sanitizarAtributos() {
    $atributos = $this->atributos();
    $sanitizado = [];
    foreach ($atributos as $key => $value) {
        $sanitizado[$key] = self::$db->quote($value); // Usa quote() para sanitizar
    }
    return $sanitizado;
}


  // Eliminar un registro
    public function eliminar() {
        // Eliminar el registro
        $query = "DELETE FROM " . static::$tabla . " WHERE id = :id LIMIT 1";
        $stmt = self::$db->prepare($query);
        return $stmt->execute(['id' => $this->id]);
    }
public static function consultarSQL($query, $params = []) {
    try {
        // Preparar la consulta
        $stmt = self::$db->prepare($query);
        
        // Ejecutar la consulta con los parámetros si existen
        $stmt->execute($params);

        // Obtener los resultados como array asociativo
        $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC); // <-- Agregar la barra invertida "\"

        // Convertir resultados en objetos de la clase actual
        $array = [];
        foreach ($resultados as $registro) {
            $array[] = static::crearObjeto($registro);
        }

        return $array;
    } catch (\PDOException $e) { // <-- También aquí
        die("Error en la consulta SQL: " . $e->getMessage());
    }
}



protected static function crearObjeto($registro) {
    $objeto = new static;

    foreach ($registro as $key => $value) {
        if (property_exists($objeto, $key)) {
            $objeto->$key = $value;
        }
    }

    return $objeto;
}

// Identificar y unir los atributos de la BD
public function atributos() {
    $atributos = [];
    foreach (static::$columnasDB as $columna) {
        if ($columna === 'id') continue;
        $atributos[$columna] = $this->$columna;
    }
    return $atributos;
}

// public function sanitizarAtributos() {
//     $atributos = $this->atributos();
//     $sanitizado = [];
//     foreach ($atributos as $key => $value) {
//         $sanitizado[$key] = pg_escape_string(self::$db, $value);
//     }
//     return $sanitizado;
// }

public function sincronizar($args = []) { 
    foreach ($args as $key => $value) {
        if (property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
        }
    }
}

// Subida de archivos
public function setImagen($imagen) {
    // Elimina la imagen previa
    if (!is_null($this->id)) {
        $this->borrarImagen();
    }
    // Asignar al atributo de imagen el nombre de la imagen
    if ($imagen) {
        $this->imagen = $imagen;
    }
}

// Elimina el archivo
public function borrarImagen() {
    // Comprobar si existe el archivo
    $existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
    if ($existeArchivo) {
        unlink(CARPETA_IMAGENES . $this->imagen);
    }
}
}
