<?php
require_once 'datos.php';

$mensaje = '';
$tipo_mensaje = '';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'guardar_nota':
            $estudiante_id = intval($_POST['estudiante_id']);
            $materia_id = intval($_POST['materia_id']);
            $nota = floatval($_POST['nota']);
            $observaciones = trim($_POST['observaciones']);

            if ($nota >= 0 && $nota <= 100) {
                if (guardarNota($estudiante_id, $materia_id, $nota, $observaciones)) {
                    $mensaje = 'Nota guardada exitosamente.';
                    $tipo_mensaje = 'exito';
                } else {
                    $mensaje = 'Error al guardar la nota.';
                    $tipo_mensaje = 'error';
                }
            } else {
                $mensaje = 'La nota debe estar entre 0 y 100.';
                $tipo_mensaje = 'error';
            }
            break;

        case 'eliminar_nota':
            $estudiante_id = intval($_POST['estudiante_id']);
            $materia_id = intval($_POST['materia_id']);

            if (eliminarNota($estudiante_id, $materia_id)) {
                $mensaje = 'Nota eliminada exitosamente.';
                $tipo_mensaje = 'exito';
            } else {
                $mensaje = 'Error al eliminar la nota.';
                $tipo_mensaje = 'error';
            }
            break;

        default:
            $mensaje = 'Acción no válida.';
            $tipo_mensaje = 'error';
    }
}

// Obtener parámetros de la URL
$vista = $_GET['vista'] ?? 'principal';
$estudiante_id = isset($_GET['estudiante']) ? intval($_GET['estudiante']) : 0;
$materia_id = isset($_GET['materia']) ? intval($_GET['materia']) : 0;
$crear_nota = isset($_GET['nueva']) ? true : false;

// Obtener datos necesarios
$estudiantes = obtenerTodosLosEstudiantes();
$materias = obtenerTodasLasMaterias();
$carreras = obtenerCarreras();

// Datos específicos según la vista
if ($estudiante_id > 0) {
    $estudiante_seleccionado = obtenerEstudiantePorId($estudiante_id);
    $notas_estudiante = obtenerNotasEstudiante($estudiante_id);
    $promedio_estudiante = calcularPromedio($estudiante_id);
}

if ($materia_id > 0) {
    $materia_seleccionada = obtenerMateriaPorId($materia_id);
    $notas_materia = obtenerNotasPorMateria($materia_id);
}

// Estadísticas generales
$total_notas = 0;
$suma_notas = 0;
foreach ($estudiantes as $estudiante) {
    $notas = obtenerNotasEstudiante($estudiante['id']);
    $total_notas += count($notas);
    foreach ($notas as $nota) {
        $suma_notas += $nota['nota'];
    }
}
$promedio_general = $total_notas > 0 ? round($suma_notas / $total_notas, 1) : 0;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estudiantes - Gestión de Notas</title>
    <link rel="stylesheet" href="estilos/style.css">
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
                    <a href="estudiantes.php" class="nav-button">
                        <span class="nav-icon">
                            <img src="assets/student-retrowave.svg" alt="Estudiantes">
                        </span>
                        <span class="nav-text">Estudiantes</span>
                    </a>
                    <a href="notas.php" class="nav-button active">
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
                <span class="breadcrumb-current">Gestión de Notas</span>
            </nav>

        <!-- Mensajes de feedback -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> fade-in">
                <strong><?php echo $tipo_mensaje === 'exito' ? '✅' : '❌'; ?></strong> 
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if ($crear_nota): ?>
            <!-- FORMULARIO PARA NUEVA NOTA -->
            <div class="detail-view fade-in">
                <div class="nota-form-header">
                    <div class="form-title-section">
                        <div class="form-icon">➕</div>
                        <div class="form-title-text">
                            <h2 class="titulo-seccion">REGISTRAR NUEVA NOTA</h2>
                            <p class="form-subtitle">Complete la información para registrar una nueva calificación</p>
                        </div>
                    </div>
                    <a href="notas.php" class="btn btn-secondary cancel-btn">← Cancelar</a>
                </div>

                <form method="POST" class="nota-form">
                    <input type="hidden" name="accion" value="guardar_nota">
                    
                    <div class="nota-form-grid">
                        <!-- Sección de Estudiante -->
                        <div class="form-section">
                            <div class="section-header">
                                <span class="section-icon">👤</span>
                                <h3>Información del Estudiante</h3>
                            </div>
                            <div class="form-group-enhanced">
                                <label for="estudiante_id">Estudiante *</label>
                                <div class="select-wrapper">
                                    <select id="estudiante_id" name="estudiante_id" required class="form-select-enhanced">
                                        <option value="">Seleccione un estudiante</option>
                                        <?php foreach ($estudiantes as $estudiante): ?>
                                            <option value="<?php echo $estudiante['id']; ?>" 
                                                    <?php echo $estudiante_id == $estudiante['id'] ? 'selected' : ''; ?>>
                                                <?php echo $estudiante['nombre'] . ' - ' . $estudiante['carrera']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección de Materia -->
                        <div class="form-section">
                            <div class="section-header">
                                <span class="section-icon">📚</span>
                                <h3>Información de la Materia</h3>
                            </div>
                            <div class="form-group-enhanced">
                                <label for="materia_id">Materia *</label>
                                <div class="select-wrapper">
                                    <select id="materia_id" name="materia_id" required class="form-select-enhanced">
                                        <option value="">Seleccione una materia</option>
                                        <?php foreach ($materias as $materia): ?>
                                            <option value="<?php echo $materia['id']; ?>"
                                                    <?php echo $materia_id == $materia['id'] ? 'selected' : ''; ?>>
                                                <?php echo $materia['nombre'] . ' - ' . $materia['carrera_nombre']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección de Calificación -->
                        <div class="form-section">
                            <div class="section-header">
                                <span class="section-icon">📊</span>
                                <h3>Calificación</h3>
                            </div>
                            <div class="form-group-enhanced">
                                <label for="nota">Calificación * (0-100)</label>
                                <div class="input-wrapper">
                                    <input type="number" id="nota" name="nota" required class="form-input-enhanced" 
                                           min="0" max="100" step="0.1" placeholder="85.5">
                                    <span class="input-suffix">pts</span>
                                </div>
                                <div class="nota-indicator">
                                    <div class="nota-bar">
                                        <div class="nota-fill" id="notaFill"></div>
                                    </div>
                                    <span class="nota-status" id="notaStatus">Ingrese una calificación</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sección de Observaciones -->
                        <div class="form-section full-width">
                            <div class="section-header">
                                <span class="section-icon">💬</span>
                                <h3>Observaciones Adicionales</h3>
                            </div>
                            <div class="form-group-enhanced">
                                <label for="observaciones">Comentarios (Opcional)</label>
                                <textarea id="observaciones" name="observaciones" class="form-textarea-enhanced" 
                                          rows="4" placeholder="Agregue comentarios adicionales sobre el desempeño del estudiante..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions-enhanced">
                        <button type="submit" class="btn btn-primary-enhanced">
                            <span class="btn-icon">💾</span>
                            <span class="btn-text">Guardar Nota</span>
                        </button>
                        <a href="notas.php" class="btn btn-secondary-enhanced">
                            <span class="btn-icon">↩️</span>
                            <span class="btn-text">Cancelar</span>
                        </a>
                    </div>
                </form>
            </div>

        <?php elseif ($estudiante_id > 0 && $estudiante_seleccionado): ?>
            <!-- VISTA DETALLADA DE NOTAS POR ESTUDIANTE -->
            <div class="detail-view fade-in">
                <div class="flex-between mb-20">
                    <a href="notas.php" class="btn btn-secondary">← Volver al Panel</a>
                    <a href="notas.php?nueva=1&estudiante=<?php echo $estudiante_id; ?>" class="btn">➕ Nueva Nota</a>
                </div>

                <div class="detail-header">
                    <div class="detail-avatar">
                        <?php echo strtoupper(substr($estudiante_seleccionado['nombre'], 0, 1) . substr(strstr($estudiante_seleccionado['nombre'], ' '), 1, 1)); ?>
                    </div>
                    <div class="detail-info">
                        <h1><?php echo $estudiante_seleccionado['nombre']; ?></h1>
                        <p><strong>Carrera:</strong> <?php echo $estudiante_seleccionado['carrera']; ?></p>
                        <p><strong>Semestre:</strong> <?php echo $estudiante_seleccionado['semestre']; ?>°</p>
                        <p><strong>Promedio General:</strong> 
                            <span class="<?php echo $promedio_estudiante >= 70 ? 'texto-neon-green' : 'texto-sunset-orange'; ?>">
                                <?php echo $promedio_estudiante; ?>
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Estadísticas del estudiante -->
                <div class="stats-grid mb-30">
                    <div class="stat-card">
                        <div class="stat-number texto-cyber-blue"><?php echo count($notas_estudiante); ?></div>
                        <div class="stat-label">Materias Cursadas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number texto-neon-green"><?php echo $promedio_estudiante; ?></div>
                        <div class="stat-label">Promedio General</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" style="color: var(--sunset-orange);">
                            <?php 
                            $aprobadas = array_filter($notas_estudiante, function($n) { return $n['nota'] >= 70; });
                            echo count($aprobadas);
                            ?>
                        </div>
                        <div class="stat-label">Materias Aprobadas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" style="color: var(--neon-pink);">
                            <?php echo count($notas_estudiante) - count($aprobadas); ?>
                        </div>
                        <div class="stat-label">Materias Reprobadas</div>
                    </div>
                </div>

                <!-- Lista de notas -->
                <div class="report-section">
                    <h2 class="titulo-seccion">📝 HISTORIAL DE CALIFICACIONES</h2>
                    <?php if (!empty($notas_estudiante)): ?>
                        <div class="notas-grid">
                            <?php foreach ($notas_estudiante as $nota): ?>
                                <div class="nota-card">
                                    <div class="nota-header">
                                        <h3><?php echo $nota['materia_nombre']; ?></h3>
                                        <div class="nota-valor <?php echo $nota['nota'] >= 70 ? 'nota-aprobado' : 'nota-desaprobado'; ?>">
                                            <?php echo $nota['nota']; ?>
                                        </div>
                                    </div>
                                    <div class="nota-details">
                                        <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($nota['fecha_registro'])); ?></p>
                                        <?php if (!empty($nota['observaciones'])): ?>
                                            <p><strong>Observaciones:</strong> <?php echo $nota['observaciones']; ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="nota-actions">
                                        <button onclick="editarNota(<?php echo $estudiante_id; ?>, <?php echo $nota['materia_id']; ?>)" 
                                                class="btn btn-small">✏️ Editar</button>
                                        <button onclick="confirmarEliminarNota(<?php echo $estudiante_id; ?>, <?php echo $nota['materia_id']; ?>)" 
                                                class="btn btn-danger btn-small">🗑️ Eliminar</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <strong>📝 Sin calificaciones</strong><br>
                            Este estudiante aún no tiene notas registradas.
                            <br><br>
                            <a href="notas.php?nueva=1&estudiante=<?php echo $estudiante_id; ?>" class="btn">Registrar primera nota</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($materia_id > 0 && $materia_seleccionada): ?>
            <!-- VISTA DETALLADA DE NOTAS POR MATERIA -->
            <div class="detail-view fade-in">
                <div class="flex-between mb-20">
                    <a href="notas.php" class="btn btn-secondary">← Volver al Panel</a>
                    <a href="notas.php?nueva=1&materia=<?php echo $materia_id; ?>" class="btn">➕ Nueva Nota</a>
                </div>

                <div class="detail-header">
                    <div class="detail-avatar">📚</div>
                    <div class="detail-info">
                        <h1><?php echo $materia_seleccionada['nombre']; ?></h1>
                        <p><strong>Carrera:</strong> <?php echo $materia_seleccionada['carrera_nombre']; ?></p>
                        <p><strong>Créditos:</strong> <?php echo $materia_seleccionada['creditos']; ?></p>
                        <?php if (!empty($materia_seleccionada['descripcion'])): ?>
                            <p><strong>Descripción:</strong> <?php echo $materia_seleccionada['descripcion']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Estadísticas de la materia -->
                <div class="stats-grid mb-30">
                    <div class="stat-card">
                        <div class="stat-number texto-cyber-blue"><?php echo count($notas_materia); ?></div>
                        <div class="stat-label">Estudiantes Evaluados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number texto-neon-green">
                            <?php 
                            $promedio_materia = count($notas_materia) > 0 ? 
                                round(array_sum(array_column($notas_materia, 'nota')) / count($notas_materia), 1) : 0;
                            echo $promedio_materia;
                            ?>
                        </div>
                        <div class="stat-label">Promedio de la Materia</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" style="color: var(--sunset-orange);">
                            <?php 
                            $aprobados = array_filter($notas_materia, function($n) { return $n['nota'] >= 70; });
                            echo count($aprobados);
                            ?>
                        </div>
                        <div class="stat-label">Estudiantes Aprobados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" style="color: var(--neon-pink);">
                            <?php echo count($notas_materia) - count($aprobados); ?>
                        </div>
                        <div class="stat-label">Estudiantes Reprobados</div>
                    </div>
                </div>

                <!-- Lista de estudiantes con sus notas -->
                <div class="report-section">
                    <h2 class="titulo-seccion">👥 CALIFICACIONES POR ESTUDIANTE</h2>
                    <?php if (!empty($notas_materia)): ?>
                        <div class="students-grid">
                            <?php foreach ($notas_materia as $nota): ?>
                                <div class="student-card" onclick="verEstudiante(<?php echo $nota['estudiante_id']; ?>)">
                                    <div class="student-header">
                                        <div class="student-avatar">
                                            <?php echo strtoupper(substr($nota['estudiante_nombre'], 0, 1) . substr(strstr($nota['estudiante_nombre'], ' '), 1, 1)); ?>
                                        </div>
                                        <div class="student-info">
                                            <h3><?php echo $nota['estudiante_nombre']; ?></h3>
                                        </div>
                                        <div class="nota-valor <?php echo $nota['nota'] >= 70 ? 'nota-aprobado' : 'nota-desaprobado'; ?>">
                                            <?php echo $nota['nota']; ?>
                                        </div>
                                    </div>
                                    <div class="student-details">
                                        <p><strong>📅 Fecha:</strong> <?php echo date('d/m/Y', strtotime($nota['fecha_registro'])); ?></p>
                                        <?php if (!empty($nota['observaciones'])): ?>
                                            <p><strong>💬 Observaciones:</strong> <?php echo $nota['observaciones']; ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="student-actions mt-10">
                                        <button onclick="event.stopPropagation(); editarNota(<?php echo $nota['estudiante_id']; ?>, <?php echo $materia_id; ?>)" 
                                                class="btn btn-small">✏️ Editar</button>
                                        <button onclick="event.stopPropagation(); confirmarEliminarNota(<?php echo $nota['estudiante_id']; ?>, <?php echo $materia_id; ?>)" 
                                                class="btn btn-danger btn-small">🗑️</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <strong>📝 Sin calificaciones</strong><br>
                            Esta materia aún no tiene estudiantes evaluados.
                            <br><br>
                            <a href="notas.php?nueva=1&materia=<?php echo $materia_id; ?>" class="btn">Registrar primera nota</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>
            <!-- VISTA PRINCIPAL - PANEL DE GESTIÓN DE NOTAS -->
            
            <!-- Filtros y acciones principales -->
            <section class="filters fade-in">
                <div class="flex-between">
                    <div class="filter-group">
                        <select onchange="filtrarPorCarrera(this.value)" class="form-select">
                            <option value="">📚 Todas las carreras</option>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?php echo htmlspecialchars($carrera); ?>">
                                    <?php echo $carrera; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <input type="text" placeholder="🔍 Buscar estudiante o materia..." 
                               class="form-input" onkeyup="buscarEnTabla(this.value)">
                    </div>
                    
                    <a href="notas.php?nueva=1" class="btn">➕ Nueva Nota</a>
                </div>
            </section>

            <!-- Estadísticas generales -->
            <section class="stats-grid fade-in">
                <div class="stat-card">
                    <div class="stat-number texto-cyber-blue"><?php echo count($estudiantes); ?></div>
                    <div class="stat-label">Estudiantes Registrados</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number texto-neon-green"><?php echo count($materias); ?></div>
                    <div class="stat-label">Materias Disponibles</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number" style="color: var(--sunset-orange);"><?php echo $promedio_general; ?></div>
                    <div class="stat-label">Promedio General</div>
                </div>
            </section>

            <!-- Secciones principales -->
            <div class="grid-sections">
                <!-- Panel de estudiantes -->
                <section class="report-section fade-in">
                    <h2 class="titulo-seccion">👥 ESTUDIANTES</h2>
                    <div class="students-mini-grid">
                        <?php foreach (array_slice($estudiantes, 0, 6) as $estudiante): ?>
                            <?php 
                            $promedio = calcularPromedio($estudiante['id']);
                            $notas_count = count(obtenerNotasEstudiante($estudiante['id']));
                            ?>
                            <div class="student-mini-card" onclick="verEstudiante(<?php echo $estudiante['id']; ?>)">
                                <div class="student-avatar">
                                    <?php echo strtoupper(substr($estudiante['nombre'], 0, 1) . substr(strstr($estudiante['nombre'], ' '), 1, 1)); ?>
                                </div>
                                <div class="student-info">
                                    <h4><?php echo $estudiante['nombre']; ?></h4>
                                    <p><?php echo $notas_count; ?> notas - Promedio: <?php echo $promedio; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-15">
                        <a href="estudiantes.php" class="btn btn-secondary">Ver todos los estudiantes</a>
                    </div>
                </section>

                <!-- Panel de materias -->
                <section class="report-section fade-in">
                    <h2 class="titulo-seccion">📚 MATERIAS</h2>
                    <div class="materias-grid">
                        <?php foreach (array_slice($materias, 0, 6) as $materia): ?>
                            <?php 
                            $notas_materia_count = count(obtenerNotasPorMateria($materia['id']));
                            $promedio_materia = 0;
                            if ($notas_materia_count > 0) {
                                $notas_temp = obtenerNotasPorMateria($materia['id']);
                                $promedio_materia = round(array_sum(array_column($notas_temp, 'nota')) / $notas_materia_count, 1);
                            }
                            ?>
                            <div class="materia-card" onclick="verMateria(<?php echo $materia['id']; ?>)">
                                <div class="materia-header">
                                    <h4><?php echo $materia['nombre']; ?></h4>
                                    <span class="carrera-tag"><?php echo $materia['carrera_nombre']; ?></span>
                                </div>
                                <div class="materia-stats">
                                    <p><?php echo $notas_materia_count; ?> estudiantes evaluados</p>
                                    <p>Promedio: <?php echo $promedio_materia; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-15">
                        <a href="notas.php?vista=materias" class="btn btn-secondary">Ver todas las materias</a>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    </div>

    <!-- Formulario oculto para eliminación de notas -->
    <form id="eliminarNotaForm" method="POST" style="display: none;">
        <input type="hidden" name="accion" value="eliminar_nota">
        <input type="hidden" name="estudiante_id" id="eliminarEstudianteId">
        <input type="hidden" name="materia_id" id="eliminarMateriaId">
    </form>

    <!-- JavaScript para funcionalidades del header -->
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

        // Funciones específicas para gestión de notas
        function verEstudiante(id) {
            window.location.href = 'notas.php?estudiante=' + id;
        }

        function verMateria(id) {
            window.location.href = 'notas.php?materia=' + id;
        }

        function editarNota(estudianteId, materiaId) {
            // Por ahora redirige al formulario de nueva nota con los parámetros
            window.location.href = `notas.php?nueva=1&estudiante=${estudianteId}&materia=${materiaId}`;
        }

        function confirmarEliminarNota(estudianteId, materiaId) {
            if (confirm('⚠️ ¿Estás seguro de que quieres eliminar esta nota?\n\nEsta acción no se puede deshacer.')) {
                document.getElementById('eliminarEstudianteId').value = estudianteId;
                document.getElementById('eliminarMateriaId').value = materiaId;
                document.getElementById('eliminarNotaForm').submit();
            }
        }

        function filtrarPorCarrera(carrera) {
            // Implementar filtrado por carrera
            console.log('Filtrar por carrera:', carrera);
        }

        function buscarEnTabla(termino) {
            // Implementar búsqueda en tiempo real
            console.log('Buscar:', termino);
        }

        // Efectos visuales
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de entrada secuencial
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });

            // Efecto hover en tarjetas
            document.querySelectorAll('.student-card, .student-mini-card, .materia-card, .nota-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
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

            // Validación del formulario de notas
            const notaForm = document.querySelector('form[name="notaForm"]');
            if (notaForm) {
                notaForm.addEventListener('submit', function(e) {
                    const nota = parseFloat(document.getElementById('nota').value);
                    if (nota < 0 || nota > 100) {
                        e.preventDefault();
                        alert('❌ La calificación debe estar entre 0 y 100');
                        return false;
                    }
                });
            }

            // Indicador visual de nota en tiempo real
            const notaInput = document.getElementById('nota');
            if (notaInput) {
                const notaFill = document.getElementById('notaFill');
                const notaStatus = document.getElementById('notaStatus');
                
                function updateNotaIndicator() {
                    const valor = parseFloat(notaInput.value) || 0;
                    const porcentaje = Math.min(Math.max(valor, 0), 100);
                    
                    // Actualizar barra de progreso
                    notaFill.style.width = porcentaje + '%';
                    
                    // Actualizar estado y color
                    let estado = '';
                    let color = '';
                    
                    if (valor === 0) {
                        estado = 'Ingrese una calificación';
                        color = 'var(--hologram-purple)';
                    } else if (valor < 60) {
                        estado = 'Reprobado';
                        color = 'var(--neon-pink)';
                    } else if (valor < 70) {
                        estado = 'Suficiente';
                        color = 'var(--sunset-orange)';
                    } else if (valor < 80) {
                        estado = 'Bueno';
                        color = 'var(--cyber-blue)';
                    } else if (valor < 90) {
                        estado = 'Muy Bueno';
                        color = 'var(--neon-green)';
                    } else {
                        estado = 'Excelente';
                        color = 'var(--neon-green)';
                    }
                    
                    notaStatus.textContent = `${valor}/100 - ${estado}`;
                    notaStatus.style.color = color;
                }
                
                notaInput.addEventListener('input', updateNotaIndicator);
                notaInput.addEventListener('change', updateNotaIndicator);
                
                // Inicializar
                updateNotaIndicator();
            }
        });
    </script>
</body>
</html>
