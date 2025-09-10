<?php
require_once 'datos.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $accion = $_POST['accion'] ?? '';

    switch($accion){
        case 'crear':
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $carrera_id = intval($_POST['carrera_id']);
            $semestre = intval($_POST['semestre']);
            $fecha_ingreso = $_POST['fecha_ingreso'];
            $telefono = trim($_POST['telefono']);
            $direccion = trim($_POST['direccion']);
            $estado = $_POST['estado'] ?? 'activo';

            if (crearEstudiante($nombre, $email, $carrera_id, $semestre, $fecha_ingreso, $telefono, $direccion, $estado)) {
                $mensaje = 'Estudiante creado exitosamente.';
                $tipo_mensaje = 'exito';
            } else {
                $mensaje = 'Error al crear estudiante.';
                $tipo_mensaje = 'error';
            }
            break;
        case 'actualizar':
            $id = intval($_POST['id']);
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $carrera_id = intval($_POST['carrera_id']);
            $semestre = intval($_POST['semestre']);
            $fecha_ingreso = $_POST['fecha_ingreso'];
            $telefono = trim($_POST['telefono']);
            $direccion = trim($_POST['direccion']);
            $estado = $_POST['estado'];

            if (actualizarEstudiante($id, $nombre, $email, $carrera_id, $semestre, $fecha_ingreso, $telefono, $direccion, $estado)) {
                $mensaje = 'Estudiante actualizado exitosamente.';
                $tipo_mensaje = 'exito';
            } else {
                $mensaje = 'Error al actualizar estudiante.';
                $tipo_mensaje = 'error';
            }
            break;
        case 'eliminar':
            $id = intval($_POST['id']);
            if (eliminarEstudiante($id)) {
                $mensaje = 'Estudiante eliminado exitosamente.';
                $tipo_mensaje = 'exito';
            } else {
                $mensaje = 'Error al eliminar estudiante.';
                $tipo_mensaje = 'error';
            }
            break;
        default:
            $mensaje = 'Acción no válida.';
            $tipo_mensaje = 'error';
    }
}

// Obtener parametros de busqueda y filtros
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$filtro_carrera = isset($_GET['carrera']) ? $_GET['carrera'] : '';
$vista_detalle = isset($_GET['detalle']) ? intval($_GET['detalle']) : 0;
$editar = isset($_GET['editar']) ? intval($_GET['editar']) : 0;
$crear_nuevo = isset($_GET['nuevo']) ? true : false;

//Obtener todas las carreras para el filtro
$carreras_disponibles = obtenerCarreras();
$carreras_completas = obtenerCarrerasCompletas();

if ($vista_detalle > 0){
    $estudiante_detalle = obtenerEstudiantePorId($vista_detalle);
    $notas_estudiante = obtenerNotasEstudiante($vista_detalle);
    $promedio_estudiante = calcularPromedio($vista_detalle);
} else if ($editar > 0) {
    $estudiante_editar = obtenerEstudiantePorId($editar);
} else {
    // Lista de estudiantes con filtros
    if (!empty($busqueda)) {
        $estudiantes = buscarEstudiantes($busqueda);
    } elseif (!empty($filtro_carrera)) {
        $estudiantes = obtenerEstudiantesPorCarrera($filtro_carrera);
    } else {
        $estudiantes = obtenerTodosLosEstudiantes();
    }
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estudiantes - Gestión de Estudiantes</title>
    <link rel="stylesheet" href="estilos/style.css">
    <link rel="stylesheet" href="estilos/estudiantes.css">
</head>
<body>
    <div class="container">
        <!-- Header principal mejorado -->
        <header class="header fade-in">
            <div class="header-content">
                <!-- Título principal -->
                <div class="header-brand">
                    <div class="brand-text">
                        <h1 class="titulo-principal neon-pulse">STUDENT SYSTEM</h1>
                        <span class="subtitle">Sistema de Gestión Académica</span>
                    </div>
                </div>
                
                <!-- Navegación centrada -->
                <nav class="nav" id="main-nav">
                    <a href="index.php" class="nav-button">
                        <span class="nav-icon">
                            <img src="assets/dashboard-icon.svg" alt="Dashboard">
                        </span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="estudiantes.php" class="nav-button active">
                        <span class="nav-icon">
                            <img src="assets/student-retrowave.svg" alt="Estudiantes">
                        </span>
                        <span class="nav-text">Estudiantes</span>
                    </a>
                    <a href="notas.php" class="nav-button">
                        <span class="nav-icon">
                            <img src="assets/exam-icon.svg" alt="Notas">
                        </span>
                        <span class="nav-text">Notas</span>
                    </a>
                    <a href="reportes.php" class="nav-button">
                        <span class="nav-icon">
                            <img src="assets/report.svg" alt="Reportes">
                        </span>
                        <span class="nav-text">Reportes</span>
                    </a>
                </nav>
                
                <!-- Información del usuario/sistema -->
                <div class="header-info">
                    <div class="system-status">
                        <span class="status-indicator active"></span>
                        <span class="status-text">Sistema Operativo</span>
                    </div>
                    <div class="current-time" id="current-time"></div>
                </div>
            </div>
            
            <!-- Botón menú móvil -->
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
        </header>

        <div class="content-wrapper">
            <!-- Breadcrumb Navigation -->
            <nav class="breadcrumb fade-in">
                <a href="index.php" class="breadcrumb-item">
                    <span class="breadcrumb-icon">
                        <img src="assets/home.svg" alt="Inicio">
                    </span>
                    Inicio
                </a>
                <span class="breadcrumb-separator">›</span>
                <span class="breadcrumb-current">Gestión de Estudiantes</span>
            </nav>

        <!-- Mensajes de feedback -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> fade-in">
                <strong><?php echo $tipo_mensaje === 'exito' ? '✅' : '❌'; ?></strong> 
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if ($crear_nuevo): ?>
            <!-- FORMULARIO DE CREACIÓN -->
            <div class="detail-view fade-in">
                <div class="flex-between mb-20">
                    <h2 class="titulo-seccion">➕ CREAR NUEVO ESTUDIANTE</h2>
                    <a href="estudiantes.php" class="btn btn-secondary">← Cancelar</a>
                </div>

                <form method="POST" class="student-form">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre Completo *</label>
                            <input type="text" id="nombre" name="nombre" required class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <label for="carrera_id">Carrera *</label>
                            <select id="carrera_id" name="carrera_id" required class="form-select">
                                <option value="">Seleccione una carrera</option>
                                <?php foreach ($carreras_completas as $carrera): ?>
                                    <option value="<?php echo $carrera['id']; ?>">
                                        <?php echo $carrera['nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="semestre">Semestre *</label>
                            <select id="semestre" name="semestre" required class="form-select">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?>° Semestre</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_ingreso">Fecha de Ingreso *</label>
                            <input type="date" id="fecha_ingreso" name="fecha_ingreso" required class="form-input" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" class="form-input" placeholder="+505 0000-0000">
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="direccion">Dirección</label>
                            <input type="text" id="direccion" name="direccion" class="form-input" placeholder="Ciudad, País">
                        </div>
                        
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado" class="form-select">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="graduado">Graduado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn">💾 Crear Estudiante</button>
                        <a href="estudiantes.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>

        <?php elseif ($editar > 0 && $estudiante_editar): ?>
            <!-- FORMULARIO DE EDICIÓN -->
            <div class="detail-view fade-in">
                <div class="flex-between mb-20">
                    <h2 class="titulo-seccion">✏️ EDITAR ESTUDIANTE</h2>
                    <a href="estudiantes.php" class="btn btn-secondary">← Cancelar</a>
                </div>

                <form method="POST" class="student-form">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id" value="<?php echo $estudiante_editar['id']; ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre Completo *</label>
                            <input type="text" id="nombre" name="nombre" required class="form-input" 
                                   value="<?php echo htmlspecialchars($estudiante_editar['nombre']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required class="form-input" 
                                   value="<?php echo htmlspecialchars($estudiante_editar['email']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="carrera_id">Carrera *</label>
                            <select id="carrera_id" name="carrera_id" required class="form-select">
                                <?php foreach ($carreras_completas as $carrera): ?>
                                    <option value="<?php echo $carrera['id']; ?>" 
                                            <?php echo $carrera['id'] == $estudiante_editar['carrera_id'] ? 'selected' : ''; ?>>
                                        <?php echo $carrera['nombre']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="semestre">Semestre *</label>
                            <select id="semestre" name="semestre" required class="form-select">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i == $estudiante_editar['semestre'] ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>° Semestre
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_ingreso">Fecha de Ingreso *</label>
                            <input type="date" id="fecha_ingreso" name="fecha_ingreso" required class="form-input" 
                                   value="<?php echo $estudiante_editar['fecha_ingreso']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" class="form-input" 
                                   value="<?php echo htmlspecialchars($estudiante_editar['telefono']); ?>">
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="direccion">Dirección</label>
                            <input type="text" id="direccion" name="direccion" class="form-input" 
                                   value="<?php echo htmlspecialchars($estudiante_editar['direccion']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado" class="form-select">
                                <option value="activo" <?php echo $estudiante_editar['estado'] == 'activo' ? 'selected' : ''; ?>>Activo</option>
                                <option value="inactivo" <?php echo $estudiante_editar['estado'] == 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                <option value="graduado" <?php echo $estudiante_editar['estado'] == 'graduado' ? 'selected' : ''; ?>>Graduado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn">💾 Actualizar Estudiante</button>
                        <a href="estudiantes.php?detalle=<?php echo $estudiante_editar['id']; ?>" class="btn btn-secondary">Ver Detalle</a>
                        <a href="estudiantes.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>

        <?php elseif ($vista_detalle > 0 && $estudiante_detalle): ?>
            <!-- VISTA DETALLADA DE ESTUDIANTE -->
            <div class="detail-view fade-in">
                <div class="flex-between mb-20">
                    <a href="estudiantes.php" class="btn btn-secondary">← Volver a la Lista</a>
                    <div>
                        <a href="estudiantes.php?editar=<?php echo $estudiante_detalle['id']; ?>" class="btn">✏️ Editar</a>
                        <button onclick="confirmarEliminar(<?php echo $estudiante_detalle['id']; ?>)" class="btn btn-danger">🗑️ Eliminar</button>
                    </div>
                </div>

                <div class="detail-header">
                    <div class="detail-avatar">
                        <?php echo strtoupper(substr($estudiante_detalle['nombre'], 0, 1) . substr(strstr($estudiante_detalle['nombre'], ' '), 1, 1)); ?>
                    </div>
                    <div class="detail-info">
                        <h1><?php echo $estudiante_detalle['nombre']; ?></h1>
                        <p><strong>Carrera:</strong> <?php echo $estudiante_detalle['carrera']; ?></p>
                        <p><strong>Email:</strong> <?php echo $estudiante_detalle['email']; ?></p>
                        <p><strong>Estado:</strong> 
                            <span class="<?php echo $estudiante_detalle['estado'] === 'activo' ? 'texto-neon-green' : 'texto-sunset-orange'; ?>">
                                <?php echo ucfirst($estudiante_detalle['estado']); ?>
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Información personal -->
                <div class="report-section">
                    <h2 class="titulo-seccion">👤 INFORMACIÓN PERSONAL</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-label">Semestre Actual</div>
                            <div class="stat-number texto-cyber-blue"><?php echo $estudiante_detalle['semestre']; ?>°</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Promedio General</div>
                            <div class="stat-number texto-neon-green"><?php echo $promedio_estudiante; ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Estado Académico</div>
                            <div class="stat-number" style="font-size: 1.2rem;">
                                <?php echo obtenerEstadoAcademico($promedio_estudiante); ?>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Materias Cursadas</div>
                            <div class="stat-number texto-cyber-blue"><?php echo count($notas_estudiante); ?></div>
                        </div>
                    </div>

                    <div class="student-details" style="margin-top: 20px;">
                        <div class="flex" style="gap: 40px; flex-wrap: wrap;">
                            <div>
                                <h3 class="titulo-card">📞 Contacto</h3>
                                <p><strong>Teléfono:</strong> <?php echo $estudiante_detalle['telefono']; ?></p>
                                <p><strong>Dirección:</strong> <?php echo $estudiante_detalle['direccion']; ?></p>
                            </div>
                            <div>
                                <h3 class="titulo-card">📅 Información Académica</h3>
                                <p><strong>Fecha de Ingreso:</strong> <?php echo date('d/m/Y', strtotime($estudiante_detalle['fecha_ingreso'])); ?></p>
                                <p><strong>Tiempo en la Universidad:</strong> 
                                    <?php 
                                    $fecha_ingreso = new DateTime($estudiante_detalle['fecha_ingreso']);
                                    $fecha_actual = new DateTime();
                                    $diferencia = $fecha_actual->diff($fecha_ingreso);
                                    echo $diferencia->y . ' años, ' . $diferencia->m . ' meses';
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de notas -->
                <div class="report-section">
                    <h2 class="titulo-seccion">📝 HISTORIAL DE NOTAS</h2>
                    <?php if (!empty($notas_estudiante)): ?>
                        <div class="notas-grid">
                            <?php foreach ($notas_estudiante as $nota): ?>
                                <div class="nota-item">
                                    <div class="nota-materia"><?php echo $nota['materia_nombre']; ?></div>
                                    <div class="nota-valor <?php echo $nota['nota'] >= 70 ? 'nota-aprobado' : 'nota-desaprobado'; ?>">
                                        <?php echo $nota['nota']; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <strong>ℹ️ Sin notas registradas</strong><br>
                            Este estudiante aún no tiene calificaciones asignadas.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>
            <!-- VISTA PRINCIPAL - LISTA DE ESTUDIANTES -->
            
            <!-- Barra de acciones -->
            <section class="filters fade-in">
                <div class="flex-between">
                    <form method="GET" class="filter-group">
                        <input type="text" 
                               name="busqueda" 
                               placeholder="🔍 Buscar por nombre o email..." 
                               value="<?php echo htmlspecialchars($busqueda); ?>"
                               class="form-input">
                        
                        <select name="carrera" class="form-select">
                            <option value="">📚 Todas las carreras</option>
                            <?php foreach ($carreras_disponibles as $carrera): ?>
                                <option value="<?php echo htmlspecialchars($carrera); ?>" 
                                        <?php echo $filtro_carrera === $carrera ? 'selected' : ''; ?>>
                                    <?php echo $carrera; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <button type="submit" class="btn">Filtrar</button>
                        
                        <?php if (!empty($busqueda) || !empty($filtro_carrera)): ?>
                            <a href="estudiantes.php" class="btn btn-secondary">Limpiar</a>
                        <?php endif; ?>
                    </form>
                    
                    <a href="estudiantes.php?nuevo=1" class="btn">➕ Nuevo Estudiante</a>
                </div>
            </section>

            <!-- Estadísticas de la vista actual -->
            <section class="stats-grid fade-in">
                <div class="stat-card">
                    <div class="stat-number numero-destacado"><?php echo count($estudiantes); ?></div>
                    <div class="stat-label">
                        <?php 
                        if (!empty($busqueda)) {
                            echo "Resultados de búsqueda";
                        } elseif (!empty($filtro_carrera)) {
                            echo "Estudiantes de " . $filtro_carrera;
                        } else {
                            echo "Total de Estudiantes";
                        }
                        ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number texto-cyber-blue">
                        <?php 
                        $activos = array_filter($estudiantes, function($est) { 
                            return $est['estado'] === 'activo'; 
                        });
                        echo count($activos);
                        ?>
                    </div>
                    <div class="stat-label">Estudiantes Activos</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number texto-neon-green">
                        <?php 
                        $carreras_unicas = array_unique(array_column($estudiantes, 'carrera'));
                        echo count($carreras_unicas);
                        ?>
                    </div>
                    <div class="stat-label">Carreras Representadas</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number" style="color: var(--sunset-orange);">
                        <?php 
                        $promedio_general = 0;
                        $contador = 0;
                        foreach ($estudiantes as $est) {
                            $prom = calcularPromedio($est['id']);
                            if ($prom > 0) {
                                $promedio_general += $prom;
                                $contador++;
                            }
                        }
                        echo $contador > 0 ? round($promedio_general / $contador, 1) : 0;
                        ?>
                    </div>
                    <div class="stat-label">Promedio del Grupo</div>
                </div>
            </section>

            <!-- Lista de estudiantes -->
            <section class="report-section fade-in">
                <h2 class="titulo-seccion">
                    👥 LISTADO DE ESTUDIANTES
                    <?php if (!empty($busqueda)): ?>
                        <small style="font-size: 0.7em; color: var(--hologram-purple);">
                            - Búsqueda: "<?php echo htmlspecialchars($busqueda); ?>"
                        </small>
                    <?php elseif (!empty($filtro_carrera)): ?>
                        <small style="font-size: 0.7em; color: var(--hologram-purple);">
                            - Carrera: <?php echo htmlspecialchars($filtro_carrera); ?>
                        </small>
                    <?php endif; ?>
                </h2>

                <?php if (!empty($estudiantes)): ?>
                    <div class="students-grid">
                        <?php foreach ($estudiantes as $estudiante): ?>
                            <?php 
                            $iniciales = strtoupper(substr($estudiante['nombre'], 0, 1) . substr(strstr($estudiante['nombre'], ' '), 1, 1));
                            $promedio = calcularPromedio($estudiante['id']);
                            $estado_academico = obtenerEstadoAcademico($promedio);
                            
                            // Determinar clase de promedio
                            $clase_promedio = 'promedio-regular';
                            if ($promedio >= 90) $clase_promedio = 'promedio-excelente';
                            elseif ($promedio >= 80) $clase_promedio = 'promedio-muy-bueno';
                            elseif ($promedio >= 70) $clase_promedio = 'promedio-bueno';
                            else $clase_promedio = 'promedio-riesgo';
                            ?>
                            
                            <div class="student-card" onclick="verDetalle(<?php echo $estudiante['id']; ?>)">
                                <div class="student-header">
                                    <div class="student-avatar"><?php echo $iniciales; ?></div>
                                    <div class="student-info">
                                        <h3><?php echo $estudiante['nombre']; ?></h3>
                                        <p class="carrera"><?php echo $estudiante['carrera']; ?></p>
                                    </div>
                                </div>
                                
                                <div class="student-details">
                                    <p><strong>📧 Email:</strong> <?php echo $estudiante['email']; ?></p>
                                    <p><strong>📚 Semestre:</strong> <?php echo $estudiante['semestre']; ?>°</p>
                                    <p><strong>📍 Ubicación:</strong> <?php echo $estudiante['direccion']; ?></p>
                                    <p><strong>📅 Ingreso:</strong> <?php echo date('d/m/Y', strtotime($estudiante['fecha_ingreso'])); ?></p>
                                    <p><strong>📞 Teléfono:</strong> <?php echo $estudiante['telefono']; ?></p>
                                    
                                    <?php if ($promedio > 0): ?>
                                        <div class="promedio-badge <?php echo $clase_promedio; ?>">
                                            Promedio: <?php echo $promedio; ?> - <?php echo $estado_academico; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="promedio-badge promedio-riesgo">
                                            Sin notas registradas
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="student-actions mt-10">
                                    <button onclick="event.stopPropagation(); verDetalle(<?php echo $estudiante['id']; ?>)" 
                                            class="btn btn-secondary" style="font-size: 0.8rem; padding: 5px 10px;">
                                        👁️ Ver
                                    </button>
                                    <button onclick="event.stopPropagation(); editarEstudiante(<?php echo $estudiante['id']; ?>)" 
                                            class="btn" style="font-size: 0.8rem; padding: 5px 10px;">
                                        ✏️ Editar
                                    </button>
                                    <button onclick="event.stopPropagation(); confirmarEliminar(<?php echo $estudiante['id']; ?>)" 
                                            class="btn btn-danger" style="font-size: 0.8rem; padding: 5px 10px;">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <strong>🔍 Sin resultados</strong><br>
                        No se encontraron estudiantes que coincidan con los criterios de búsqueda.
                        <br><br>
                        <a href="estudiantes.php" class="btn">Ver todos los estudiantes</a>
                        <a href="estudiantes.php?nuevo=1" class="btn">Crear nuevo estudiante</a>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
        </div>
    </div>

    <!-- Formulario oculto para eliminación -->
    <form id="eliminarForm" method="POST" style="display: none;">
        <input type="hidden" name="accion" value="eliminar">
        <input type="hidden" name="id" id="eliminarId">
    </form>

    <!-- JavaScript para funcionalidades mejoradas -->
    <script>
        // Reloj en tiempo real
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const dateString = now.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.innerHTML = `${timeString}<br><small style="opacity: 0.7;">${dateString}</small>`;
            }
        }

        // Actualizar cada segundo
        setInterval(updateTime, 1000);
        updateTime(); // Llamada inicial

        // Toggle del menú móvil
        function toggleMobileMenu() {
            const nav = document.getElementById('main-nav');
            nav.classList.toggle('active');
        }

        // Funciones existentes del sistema
        function verDetalle(id) {
            window.location.href = 'estudiantes.php?ver=' + id;
        }

        function editarEstudiante(id) {
            window.location.href = 'estudiantes.php?editar=' + id;
        }

        function confirmarEliminar(id) {
            if (confirm('⚠️ ¿Estás seguro de que quieres eliminar este estudiante?\n\nEsta acción no se puede deshacer.')) {
                document.getElementById('eliminarId').value = id;
                document.getElementById('eliminarForm').submit();
            }
        }

        function crearNuevo() {
            window.location.href = 'estudiantes.php?nuevo=1';
        }

        // Efectos visuales mejorados
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de entrada secuencial
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });

            // Efecto hover en tarjetas de estudiantes
            document.querySelectorAll('.student-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                    this.style.boxShadow = '0 15px 40px rgba(0, 212, 255, 0.25)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                    this.style.boxShadow = '';
                });
            });

            // Efecto de brillo en el header al hacer scroll
            window.addEventListener('scroll', function() {
                const header = document.querySelector('.header');
                if (header) {
                    const scrolled = window.pageYOffset;
                    
                    if (scrolled > 50) {
                        header.style.boxShadow = `
                            0 12px 40px rgba(0, 212, 255, 0.25),
                            0 0 0 1px rgba(0, 212, 255, 0.4),
                            inset 0 1px 0 rgba(255, 255, 255, 0.15)
                        `;
                        header.style.backdropFilter = 'blur(25px)';
                    } else {
                        header.style.boxShadow = `
                            0 8px 32px rgba(0, 212, 255, 0.15),
                            0 0 0 1px rgba(0, 212, 255, 0.3),
                            inset 0 1px 0 rgba(255, 255, 255, 0.1)
                        `;
                        header.style.backdropFilter = 'blur(20px)';
                    }
                }
            });
        });
    </script>
</body>
</html>