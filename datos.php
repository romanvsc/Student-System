<?php
// Archivo de conexión y funciones para el sistema de estudiantes con MySQLi

// Configuración de la base de datos
class Database {
    private $host = 'localhost';
    private $db_name = 'student_system';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        if ($this->conn === null) {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            // Verificar conexión
            if ($this->conn->connect_error) {
                die("Error de conexión: " . $this->conn->connect_error);
            }
            
            // Establecer charset UTF-8
            $this->conn->set_charset("utf8mb4");
        }
        
        return $this->conn;
    }
}

// Instancia global de la base de datos
$database = new Database();
$mysqli = $database->getConnection();

// ===== CRUD PARA ESTUDIANTES =====

/**
 * CREATE - Crear nuevo estudiante
 */
function crearEstudiante($nombre, $email, $carrera_id, $semestre, $fecha_ingreso, $telefono, $direccion, $estado = 'activo') {
    global $mysqli;
    
    $query = "INSERT INTO estudiantes (nombre, email, carrera_id, semestre, fecha_ingreso, telefono, direccion, estado) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssiissss', $nombre, $email, $carrera_id, $semestre, $fecha_ingreso, $telefono, $direccion, $estado);
    
    if ($stmt->execute()) {
        $id = $mysqli->insert_id;
        $stmt->close();
        return $id;
    }
    $stmt->close();
    return false;
}

/**
 * READ - Obtiene todos los estudiantes
 */
function obtenerTodosLosEstudiantes() {
    global $mysqli;
    
    $query = "SELECT e.*, c.nombre as carrera 
              FROM estudiantes e 
              INNER JOIN carreras c ON e.carrera_id = c.id 
              ORDER BY e.nombre";
    
    $result = $mysqli->query($query);
    
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

/**
 * READ - Obtiene un estudiante por ID
 */
function obtenerEstudiantePorId($id) {
    global $mysqli;
    
    $query = "SELECT e.*, c.nombre as carrera 
              FROM estudiantes e 
              INNER JOIN carreras c ON e.carrera_id = c.id 
              WHERE e.id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $estudiante = $result->fetch_assoc();
    $stmt->close();
    
    return $estudiante;
}

/**
 * UPDATE - Actualizar estudiante
 */
function actualizarEstudiante($id, $nombre, $email, $carrera_id, $semestre, $fecha_ingreso, $telefono, $direccion, $estado) {
    global $mysqli;
    
    $query = "UPDATE estudiantes 
              SET nombre = ?, email = ?, carrera_id = ?, 
                  semestre = ?, fecha_ingreso = ?, 
                  telefono = ?, direccion = ?, estado = ?,
                  updated_at = CURRENT_TIMESTAMP
              WHERE id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssiissssi', $nombre, $email, $carrera_id, $semestre, $fecha_ingreso, $telefono, $direccion, $estado, $id);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * DELETE - Eliminar estudiante (y sus notas en cascada)
 */
function eliminarEstudiante($id) {
    global $mysqli;
    
    $query = "DELETE FROM estudiantes WHERE id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}// ===== CRUD PARA CARRERAS =====

/**
 * CREATE - Crear nueva carrera
 */
function crearCarrera($nombre, $descripcion = '') {
    global $mysqli;
    
    $query = "INSERT INTO carreras (nombre, descripcion) VALUES (?, ?)";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $nombre, $descripcion);
    
    if ($stmt->execute()) {
        $id = $mysqli->insert_id;
        $stmt->close();
        return $id;
    }
    $stmt->close();
    return false;
}

/**
 * READ - Obtiene todas las carreras disponibles
 */
function obtenerCarreras() {
    global $mysqli;
    
    $query = "SELECT nombre FROM carreras ORDER BY nombre";
    
    $result = $mysqli->query($query);
    
    if ($result) {
        $carreras = [];
        while ($row = $result->fetch_assoc()) {
            $carreras[] = $row['nombre'];
        }
        return $carreras;
    }
    return [];
}

/**
 * READ - Obtiene todas las carreras con detalles completos
 */
function obtenerCarrerasCompletas() {
    global $mysqli;
    
    $query = "SELECT * FROM carreras ORDER BY nombre";
    
    $result = $mysqli->query($query);
    
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

/**
 * READ - Obtiene una carrera por ID
 */
function obtenerCarreraPorId($id) {
    global $mysqli;
    
    $query = "SELECT * FROM carreras WHERE id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $carrera = $result->fetch_assoc();
    $stmt->close();
    
    return $carrera;
}

/**
 * UPDATE - Actualizar carrera
 */
function actualizarCarrera($id, $nombre, $descripcion) {
    global $mysqli;
    
    $query = "UPDATE carreras SET nombre = ?, descripcion = ? WHERE id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssi', $nombre, $descripcion, $id);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * DELETE - Eliminar carrera (solo si no tiene estudiantes)
 */
function eliminarCarrera($id) {
    global $mysqli;
    
    // Verificar si hay estudiantes en esta carrera
    $check_query = "SELECT COUNT(*) as count FROM estudiantes WHERE carrera_id = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param('i', $id);
    $check_stmt->execute();
    
    $check_result = $check_stmt->get_result();
    $count = $check_result->fetch_assoc()['count'];
    $check_stmt->close();
    
    if ($count > 0) {
        return false; // No se puede eliminar, hay estudiantes
    }
    
    $query = "DELETE FROM carreras WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// ===== CRUD PARA MATERIAS =====

/**
 * CREATE - Crear nueva materia
 */
function crearMateria($nombre, $carrera_id, $creditos = 3, $descripcion = '') {
    global $mysqli;
    
    $query = "INSERT INTO materias (nombre, carrera_id, creditos, descripcion) 
              VALUES (?, ?, ?, ?)";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('siis', $nombre, $carrera_id, $creditos, $descripcion);
    
    if ($stmt->execute()) {
        $id = $mysqli->insert_id;
        $stmt->close();
        return $id;
    }
    $stmt->close();
    return false;
}

/**
 * READ - Obtiene todas las materias de una carrera
 */
function obtenerMateriasPorCarrera($carrera_id) {
    global $mysqli;
    
    $query = "SELECT * FROM materias WHERE carrera_id = ? ORDER BY nombre";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $carrera_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $materias = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $materias;
}

/**
 * READ - Obtiene todas las materias
 */
function obtenerTodasLasMaterias() {
    global $mysqli;
    
    $query = "SELECT m.*, c.nombre as carrera_nombre 
              FROM materias m 
              INNER JOIN carreras c ON m.carrera_id = c.id 
              ORDER BY c.nombre, m.nombre";
    
    $result = $mysqli->query($query);
    
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

/**
 * READ - Obtiene una materia por ID
 */
function obtenerMateriaPorId($id) {
    global $mysqli;
    
    $query = "SELECT m.*, c.nombre as carrera_nombre 
              FROM materias m 
              INNER JOIN carreras c ON m.carrera_id = c.id 
              WHERE m.id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $materia = $result->fetch_assoc();
    $stmt->close();
    
    return $materia;
}

/**
 * UPDATE - Actualizar materia
 */
function actualizarMateria($id, $nombre, $carrera_id, $creditos, $descripcion) {
    global $mysqli;
    
    $query = "UPDATE materias 
              SET nombre = ?, carrera_id = ?, 
                  creditos = ?, descripcion = ? 
              WHERE id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('siisi', $nombre, $carrera_id, $creditos, $descripcion, $id);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * DELETE - Eliminar materia (solo si no tiene notas)
 */
function eliminarMateria($id) {
    global $mysqli;
    
    // Verificar si hay notas para esta materia
    $check_query = "SELECT COUNT(*) as count FROM notas WHERE materia_id = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param('i', $id);
    $check_stmt->execute();
    
    $check_result = $check_stmt->get_result();
    $count = $check_result->fetch_assoc()['count'];
    $check_stmt->close();
    
    if ($count > 0) {
        return false; // No se puede eliminar, hay notas
    }
    
    $query = "DELETE FROM materias WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// ===== CRUD PARA NOTAS =====

/**
 * CREATE/UPDATE - Inserta o actualiza una nota
 */
function guardarNota($estudiante_id, $materia_id, $nota, $observaciones = '') {
    global $mysqli;
    
    $query = "INSERT INTO notas (estudiante_id, materia_id, nota, observaciones) 
              VALUES (?, ?, ?, ?)
              ON DUPLICATE KEY UPDATE 
              nota = VALUES(nota), 
              observaciones = VALUES(observaciones),
              updated_at = CURRENT_TIMESTAMP";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('iids', $estudiante_id, $materia_id, $nota, $observaciones);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * READ - Obtiene las notas de un estudiante
 */
function obtenerNotasEstudiante($estudiante_id) {
    global $mysqli;
    
    $query = "SELECT n.*, m.nombre as materia_nombre, c.nombre as carrera_nombre
              FROM notas n 
              INNER JOIN materias m ON n.materia_id = m.id 
              INNER JOIN carreras c ON m.carrera_id = c.id
              WHERE n.estudiante_id = ? 
              ORDER BY c.nombre, m.nombre";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $estudiante_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $notas = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $notas;
}

/**
 * READ - Obtiene todas las notas de una materia
 */
function obtenerNotasPorMateria($materia_id) {
    global $mysqli;
    
    $query = "SELECT n.*, e.nombre as estudiante_nombre, e.email
              FROM notas n 
              INNER JOIN estudiantes e ON n.estudiante_id = e.id 
              WHERE n.materia_id = ? 
              ORDER BY e.nombre";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $materia_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $notas = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $notas;
}

/**
 * DELETE - Eliminar una nota específica
 */
function eliminarNota($estudiante_id, $materia_id) {
    global $mysqli;
    
    $query = "DELETE FROM notas WHERE estudiante_id = ? AND materia_id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ii', $estudiante_id, $materia_id);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * DELETE - Eliminar todas las notas de un estudiante
 */
function eliminarNotasEstudiante($estudiante_id) {
    global $mysqli;
    
    $query = "DELETE FROM notas WHERE estudiante_id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $estudiante_id);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// ===== FUNCIONES DE BÚSQUEDA Y FILTRADO =====

/**
 * Calcula el promedio de un estudiante
 */
function calcularPromedio($estudiante_id) {
    global $mysqli;
    
    $query = "SELECT AVG(nota) as promedio FROM notas WHERE estudiante_id = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $estudiante_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row ? round($row['promedio'], 2) : 0;
}

/**
 * Estado académico basado en promedio
 */
function obtenerEstadoAcademico($promedio) {
    if($promedio >= 90) {
        return 'Excelente';
    } elseif($promedio >= 80) {
        return 'Bueno';
    } elseif($promedio >= 70) {
        return 'Regular';
    } else {
        return 'Necesita Mejorar';
    }
}

/**
 * Obtiene estudiantes por carrera
 */
function obtenerEstudiantesPorCarrera($carrera_nombre) {
    global $mysqli;
    
    $query = "SELECT e.*, c.nombre as carrera 
              FROM estudiantes e 
              INNER JOIN carreras c ON e.carrera_id = c.id 
              WHERE c.nombre = ? 
              ORDER BY e.nombre";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $carrera_nombre);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $estudiantes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $estudiantes;
}

/**
 * Busca estudiantes por nombre o email
 */
function buscarEstudiantes($termino) {
    global $mysqli;
    
    $termino = '%' . $termino . '%';
    
    $query = "SELECT e.*, c.nombre as carrera 
              FROM estudiantes e 
              INNER JOIN carreras c ON e.carrera_id = c.id 
              WHERE e.nombre LIKE ? OR e.email LIKE ? 
              ORDER BY e.nombre";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $termino, $termino);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $estudiantes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $estudiantes;
}

// ===== FUNCIONES DE ESTADÍSTICAS =====

/**
 * Obtiene estadísticas generales del sistema
 */
function obtenerEstadisticasGenerales() {
    global $mysqli;
    
    // Total de estudiantes
    $query1 = "SELECT COUNT(*) as total FROM estudiantes WHERE estado = 'activo'";
    $result1 = $mysqli->query($query1);
    $total_estudiantes = $result1->fetch_assoc()['total'];
    
    // Estudiantes con notas
    $query2 = "SELECT COUNT(DISTINCT estudiante_id) as total FROM notas";
    $result2 = $mysqli->query($query2);
    $estudiantes_con_notas = $result2->fetch_assoc()['total'];
    
    // Total de carreras
    $query3 = "SELECT COUNT(*) as total FROM carreras";
    $result3 = $mysqli->query($query3);
    $total_carreras = $result3->fetch_assoc()['total'];
    
    // Promedio general
    $query4 = "SELECT AVG(nota) as promedio FROM notas";
    $result4 = $mysqli->query($query4);
    $promedio_general = round($result4->fetch_assoc()['promedio'], 2);
    
    // Nombres de carreras
    $carreras = obtenerCarreras();
    
    return [
        'total_estudiantes' => $total_estudiantes,
        'estudiantes_con_notas' => $estudiantes_con_notas,
        'total_carreras' => $total_carreras,
        'promedio_general' => $promedio_general,
        'carreras' => $carreras
    ];
}

/**
 * Obtiene el ranking de estudiantes ordenado por promedio
 */
function obtenerRankingEstudiantes() {
    global $mysqli;
    
    $query = "SELECT e.*, c.nombre as carrera, AVG(n.nota) as promedio
              FROM estudiantes e 
              INNER JOIN carreras c ON e.carrera_id = c.id 
              LEFT JOIN notas n ON e.id = n.estudiante_id 
              WHERE e.estado = 'activo'
              GROUP BY e.id 
              HAVING promedio IS NOT NULL 
              ORDER BY promedio DESC";
    
    $result = $mysqli->query($query);
    
    $ranking = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $promedio = round($row['promedio'], 2);
            $ranking[] = [
                'estudiante' => [
                    'id' => $row['id'],
                    'nombre' => $row['nombre'],
                    'email' => $row['email'],
                    'carrera' => $row['carrera'],
                    'semestre' => $row['semestre'],
                    'fecha_ingreso' => $row['fecha_ingreso'],
                    'telefono' => $row['telefono'],
                    'direccion' => $row['direccion'],
                    'estado' => $row['estado']
                ],
                'promedio' => $promedio,
                'estado_academico' => obtenerEstadoAcademico($promedio)
            ];
        }
    }
    
    return $ranking;
}

/**
 * Obtiene estadísticas por materia
 */
function obtenerEstadisticasPorMateria() {
    global $mysqli;
    
    $query = "SELECT m.nombre as materia, c.nombre as carrera,
                     COUNT(n.id) as total_estudiantes,
                     AVG(n.nota) as promedio,
                     MIN(n.nota) as nota_minima,
                     MAX(n.nota) as nota_maxima
              FROM materias m 
              INNER JOIN carreras c ON m.carrera_id = c.id
              LEFT JOIN notas n ON m.id = n.materia_id 
              GROUP BY m.id, m.nombre, c.nombre
              ORDER BY c.nombre, promedio DESC";
    
    $result = $mysqli->query($query);
    
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

// ===== FUNCIONES AUXILIARES =====

/**
 * Función auxiliar para verificar la conexión
 */
function verificarConexion() {
    global $mysqli;
    
    try {
        $result = $mysqli->query("SELECT 1");
        return $result !== false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Función auxiliar para obtener información de la base de datos
 */
function obtenerInfoBaseDatos() {
    global $mysqli;
    
    try {
        $info = [];
        
        // Versión de MySQL
        $result = $mysqli->query("SELECT VERSION() as version");
        if ($result) {
            $info['mysql_version'] = $result->fetch_assoc()['version'];
        }
        
        // Nombre de la base de datos
        $result = $mysqli->query("SELECT DATABASE() as db_name");
        if ($result) {
            $info['database_name'] = $result->fetch_assoc()['db_name'];
        }
        
        // Conteo de tablas
        $result = $mysqli->query("SHOW TABLES");
        if ($result) {
            $info['total_tables'] = $result->num_rows;
        }
        
        return $info;
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

// ===== SISTEMA DE AUTENTICACIÓN =====

/**
 * Crear tabla de usuarios (ejecutar una sola vez)
 */
function crearTablaUsuarios() {
    global $mysqli;
    
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        nombre_completo VARCHAR(255) NOT NULL,
        tipo_usuario ENUM('administrador', 'alumno') NOT NULL DEFAULT 'alumno',
        estudiante_id INT NULL,
        avatar VARCHAR(255) NULL,
        activo BOOLEAN DEFAULT TRUE,
        ultimo_acceso DATETIME NULL,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE SET NULL,
        INDEX idx_username (username),
        INDEX idx_email (email),
        INDEX idx_tipo_usuario (tipo_usuario)
    )";
    
    if ($mysqli->query($sql)) {
        // Crear usuario administrador por defecto
        crearUsuarioAdmin();
        return true;
    }
    return false;
}

/**
 * Crear usuario administrador por defecto
 */
function crearUsuarioAdmin() {
    global $mysqli;
    
    // Verificar si ya existe un administrador
    $check = $mysqli->query("SELECT id FROM usuarios WHERE tipo_usuario = 'administrador' LIMIT 1");
    if ($check && $check->num_rows > 0) {
        return false; // Ya existe un admin
    }
    
    $username = 'admin';
    $email = 'admin@studentsystem.com';
    $password = 'admin123'; // Cambiar en producción
    $nombre_completo = 'Administrador del Sistema';
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO usuarios (username, email, password_hash, nombre_completo, tipo_usuario) 
            VALUES (?, ?, ?, ?, 'administrador')";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssss', $username, $email, $password_hash, $nombre_completo);
    
    return $stmt->execute();
}

/**
 * Crear nuevo usuario
 */
function crearUsuario($username, $email, $password, $nombre_completo, $tipo_usuario = 'alumno', $estudiante_id = null) {
    global $mysqli;
    
    // Verificar que el username y email no existan
    $check_sql = "SELECT id FROM usuarios WHERE username = ? OR email = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    $check_stmt->bind_param('ss', $username, $email);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        return false; // Usuario o email ya existe
    }
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO usuarios (username, email, password_hash, nombre_completo, tipo_usuario, estudiante_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sssssi', $username, $email, $password_hash, $nombre_completo, $tipo_usuario, $estudiante_id);
    
    if ($stmt->execute()) {
        return $mysqli->insert_id;
    }
    return false;
}

/**
 * Autenticar usuario
 */
function autenticarUsuario($username, $password) {
    global $mysqli;
    
    $sql = "SELECT * FROM usuarios WHERE (username = ? OR email = ?) AND activo = TRUE";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $username, $username);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    
    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        // Actualizar último acceso
        actualizarUltimoAcceso($usuario['id']);
        
        // No devolver la contraseña
        unset($usuario['password_hash']);
        return $usuario;
    }
    
    return false;
}

/**
 * Obtener usuario por ID
 */
function obtenerUsuarioPorId($id) {
    global $mysqli;
    
    $sql = "SELECT u.*, e.nombre as estudiante_nombre, e.carrera_id
            FROM usuarios u 
            LEFT JOIN estudiantes e ON u.estudiante_id = e.id 
            WHERE u.id = ? AND u.activo = TRUE";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    
    if ($usuario) {
        unset($usuario['password_hash']);
    }
    
    return $usuario;
}

/**
 * Actualizar último acceso
 */
function actualizarUltimoAcceso($usuario_id) {
    global $mysqli;
    
    $sql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $usuario_id);
    
    return $stmt->execute();
}

/**
 * Cambiar contraseña
 */
function cambiarPassword($usuario_id, $nueva_password) {
    global $mysqli;
    
    $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
    
    $sql = "UPDATE usuarios SET password_hash = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('si', $password_hash, $usuario_id);
    
    return $stmt->execute();
}

/**
 * Obtener todos los usuarios (solo admin)
 */
function obtenerTodosUsuarios() {
    global $mysqli;
    
    $sql = "SELECT u.id, u.username, u.email, u.nombre_completo, u.tipo_usuario, 
                   u.ultimo_acceso, u.fecha_creacion, u.activo,
                   e.nombre as estudiante_nombre
            FROM usuarios u 
            LEFT JOIN estudiantes e ON u.estudiante_id = e.id 
            ORDER BY u.fecha_creacion DESC";
    
    $result = $mysqli->query($sql);
    
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

/**
 * Activar/Desactivar usuario
 */
function toggleUsuarioActivo($usuario_id) {
    global $mysqli;
    
    $sql = "UPDATE usuarios SET activo = !activo WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $usuario_id);
    
    return $stmt->execute();
}

// Inicializar tabla de usuarios si no existe
$check_table = $mysqli->query("SHOW TABLES LIKE 'usuarios'");
if ($check_table->num_rows === 0) {
    crearTablaUsuarios();
}

// Verificar conexión al cargar el archivo
if (!verificarConexion()) {
    die("Error: No se pudo conectar a la base de datos. Verifique que MySQL esté ejecutándose y que la base de datos 'student_system' exista.");
}
?>