    <?php
    class Database
    {
        public $conn;

        public function __construct()
        {
            $host = "localhost";   // o la IP del servidor
            $usuario = "root";     // tu usuario de MySQL
            $clave = "";           // tu contraseña de MySQL
            $bd = "homeplus"; // nombre de la base de datos
            $puerto = 3306;

            // Crear conexión
            $this->conn = new mysqli($host, $usuario, $clave, $bd, $puerto);

            // Verificar conexión
            if ($this->conn->connect_error) {
                die("❌ Error de conexión: " . $this->conn->connect_error);
            }
            //echo "✅ Conexión exitosa a la base de datos";
        }
    }
