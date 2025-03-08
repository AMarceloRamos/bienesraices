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

    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key} = :{$key}";
        }

        $query = "UPDATE " . static::$tabla . " SET " . join(', ', $valores) . " WHERE id = :id LIMIT 1";

        // Preparar y ejecutar la consulta
        $stmt = self::$db->prepare($query);
        $atributos['id'] = $this->id;  // Aseguramos que el ID esté en los parámetros
        return $stmt->execute($atributos);
    }

  // Eliminar un registro
    public function eliminar() {
        // Eliminar el registro
        $query = "DELETE FROM " . static::$tabla . " WHERE id = :id LIMIT 1";
        $stmt = self::$db->prepare($query);
        return $stmt->execute(['id' => $this->id]);
    }
}
