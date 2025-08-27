<?php
// Archivo de conexión y funciones para el sistema de estudiantes con MySQL

// Configuración de la base de datos
class Database {
    private $host = 'localhost';
    private $db_name = 'student_system';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
            die();
        }
        
        return $this->conn;
    }
}

// Instancia global de la base de datos
$database = new Database();
$pdo = $database->getConnection();

/**
 * Obtiene todos los estudiantes
 */
function obtenerTodosLosEstudiantes() {
    global $pdo;
    
    $query = "SELECT e.*, c.nombre as carrera 
            FROM estudiantes e 
            INNER JOIN carreras c ON e.carrera_id = c.id 
            ORDER BY e.nombre";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Obtiene un estudiante por ID
 */
function obtenerEstudiantePorId($id) {
    global $pdo;
    
    $query = "SELECT e.*, c.nombre as carrera 
            FROM estudiantes e 
            INNER JOIN carreras c ON e.carrera_id = c.id 
            WHERE e.id = :id";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch();
}

/**
 * Calcula el promedio de un estudiante
 */
function calcularPromedio($estudiante_id) {
    global $pdo;
    
    $query = "SELECT AVG(nota) as promedio FROM notas WHERE estudiante_id = :estudiante_id";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch();
    return $result ? round($result['promedio'], 2) : 0;
}

/**
 * Obtiene las notas de un estudiante
 */
function obtenerNotasEstudiante($estudiante_id) {
    global $pdo;
    
    $query = "SELECT n.*, m.nombre as materia_nombre 
            FROM notas n 
            INNER JOIN materias m ON n.materia_id = m.id 
            WHERE n.estudiante_id = :estudiante_id 
            ORDER BY m.nombre";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
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
    global $pdo;
    
    $query = "SELECT e.*, c.nombre as carrera 
            FROM estudiantes e 
            INNER JOIN carreras c ON e.carrera_id = c.id 
            WHERE c.nombre = :carrera_nombre 
            ORDER BY e.nombre";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':carrera_nombre', $carrera_nombre, PDO::PARAM_STR);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Busca estudiantes por nombre o email
 */
function buscarEstudiantes($termino) {
    global $pdo;
    
    $termino = '%' . $termino . '%';
    
    $query = "SELECT e.*, c.nombre as carrera 
            FROM estudiantes e 
            INNER JOIN carreras c ON e.carrera_id = c.id 
            WHERE e.nombre LIKE :termino OR e.email LIKE :termino 
            ORDER BY e.nombre";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Obtiene todas las carreras disponibles
 */
function obtenerCarreras() {
    global $pdo;
    
    $query = "SELECT nombre FROM carreras ORDER BY nombre";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $carreras = $stmt->fetchAll();
    return array_column($carreras, 'nombre');
}

/**
 * Obtiene estadísticas generales del sistema
 */
function obtenerEstadisticasGenerales() {
    global $pdo;
    
    // Total de estudiantes
    $query1 = "SELECT COUNT(*) as total FROM estudiantes WHERE estado = 'activo'";
    $stmt1 = $pdo->prepare($query1);
    $stmt1->execute();
    $total_estudiantes = $stmt1->fetch()['total'];
    
    // Estudiantes con notas
    $query2 = "SELECT COUNT(DISTINCT estudiante_id) as total FROM notas";
    $stmt2 = $pdo->prepare($query2);
    $stmt2->execute();
    $estudiantes_con_notas = $stmt2->fetch()['total'];
    
    // Total de carreras
    $query3 = "SELECT COUNT(*) as total FROM carreras";
    $stmt3 = $pdo->prepare($query3);
    $stmt3->execute();
    $total_carreras = $stmt3->fetch()['total'];
    
    // Promedio general
    $query4 = "SELECT AVG(nota) as promedio FROM notas";
    $stmt4 = $pdo->prepare($query4);
    $stmt4->execute();
    $promedio_general = round($stmt4->fetch()['promedio'], 2);
    
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
    global $pdo;
    
    $query = "SELECT e.*, c.nombre as carrera, AVG(n.nota) as promedio
            FROM estudiantes e 
            INNER JOIN carreras c ON e.carrera_id = c.id 
            LEFT JOIN notas n ON e.id = n.estudiante_id 
            WHERE e.estado = 'activo'
            GROUP BY e.id 
            HAVING promedio IS NOT NULL 
            ORDER BY promedio DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $ranking = [];
    while ($row = $stmt->fetch()) {
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
    
    return $ranking;
}

/**
 * Obtiene todas las materias de una carrera
 */
function obtenerMateriasPorCarrera($carrera_id) {
    global $pdo;
    
    $query = "SELECT * FROM materias WHERE carrera_id = :carrera_id ORDER BY nombre";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':carrera_id', $carrera_id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Inserta o actualiza una nota
 */
function guardarNota($estudiante_id, $materia_id, $nota) {
    global $pdo;
    
    $query = "INSERT INTO notas (estudiante_id, materia_id, nota) 
            VALUES (:estudiante_id, :materia_id, :nota)
            ON DUPLICATE KEY UPDATE nota = :nota_update";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
    $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
    $stmt->bindParam(':nota', $nota);
    $stmt->bindParam(':nota_update', $nota);
    
    return $stmt->execute();
}

/**
 * Obtiene estadísticas por materia
 */
function obtenerEstadisticasPorMateria() {
    global $pdo;
    
    $query = "SELECT m.nombre as materia, 
                    COUNT(n.id) as total_estudiantes,
                    AVG(n.nota) as promedio,
                    MIN(n.nota) as nota_minima,
                    MAX(n.nota) as nota_maxima
            FROM materias m 
            LEFT JOIN notas n ON m.id = n.materia_id 
            GROUP BY m.id, m.nombre 
            ORDER BY promedio DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Obtiene todas las carreras con detalles completos
 */
function obtenerCarrerasCompletas() {
    global $pdo;
    
    $query = "SELECT * FROM carreras ORDER BY nombre";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

/**
 * Función auxiliar para verificar la conexión
 */
function verificarConexion() {
    global $pdo;
    
    try {
        $query = "SELECT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Función auxiliar para obtener información de la base de datos
 */
function obtenerInfoBaseDatos() {
    global $pdo;
    
    try {
        $info = [];
        
        // Versión de MySQL
        $stmt = $pdo->query("SELECT VERSION() as version");
        $info['mysql_version'] = $stmt->fetch()['version'];
        
        // Nombre de la base de datos
        $stmt = $pdo->query("SELECT DATABASE() as db_name");
        $info['database_name'] = $stmt->fetch()['db_name'];
        
        // Conteo de tablas
        $stmt = $pdo->query("SHOW TABLES");
        $info['total_tables'] = $stmt->rowCount();
        
        return $info;
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

// Verificar conexión al cargar el archivo
if (!verificarConexion()) {
    die("Error: No se pudo conectar a la base de datos. Verifique que MySQL esté ejecutándose y que la base de datos 'student_system' exista.");
}
?>