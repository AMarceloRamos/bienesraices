<?php

namespace Model;

class Admin extends ActiveRecord {
   
    // Base DE DATOS
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'email', 'password'];

    public $id;
    public $email;
    public $password;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
    }

 

    public function validar() {
        if(!$this->email) {
            self::$errores[] = "El Email del usuario es obligatorio";
        }
        if(!$this->password) {
            self::$errores[] = "El Password del usuario es obligatorio";
        }
        return self::$errores;
    }

    public function existeUsuario() {
        // Revisar si el usuario existe.
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        $resultado = self::$db->query($query);

        if(!$resultado->rowCount()) {
            self::$errores[] = 'El Usuario No Existe';
            return;
        }

        return $resultado;
    }

   public function comprobarPassword($resultado) {
       public $autenticado = false; 
    if ($resultado instanceof PDOStatement) { // ✅ Verificar que $resultado es un objeto PDOStatement
        $usuario = $resultado->fetchObject(); // ✅ Ahora sí podemos llamar a fetchObject()
        
        if ($usuario) {
            $this->autenticado = password_verify($this->password, $usuario->password);

            if (!$this->autenticado) {
                self::$errores[] = 'El Password es Incorrecto';
            }
        } else {
            self::$errores[] = 'Usuario no encontrado';
        }
    } else {
        self::$errores[] = 'Error al obtener los datos del usuario';
    }
}


    public function autenticar() {
         // El usuario esta autenticado
         session_start();

         // Llenar el arreglo de la sesión
         $_SESSION['usuario'] = $this->email;
         $_SESSION['login'] = true;

         header('Location: /admin');
    }

}
