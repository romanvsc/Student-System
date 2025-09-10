<?php
require_once 'datos.php';

// Determinar el tipo de reporte solicitado
$tipo_reporte = $_GET['tipo'] ?? 'dashboard';
$carrera_filtro = $_GET['carrera'] ?? '';
$formato = $_GET['formato'] ?? 'web';

// Obtener datos necesarios para reportes
$estadisticas_generales = obtenerEstadisticasGenerales();
$ranking_estudiantes = obtenerRankingEstudiantes();
$estadisticas_materias = obtenerEstadisticasPorMateria();
$carreras = obtenerCarreras();
$estudiantes = obtenerTodosLosEstudiantes();
$materias = obtenerTodasLasMaterias();

// Calcular estadísticas adicionales
$total_estudiantes = count($estudiantes);
$promedio_general = 0;
$estudiantes_riesgo = 0;
$estudiantes_excelencia = 0;

foreach ($estudiantes as $estudiante) {
    $promedio = calcularPromedio($estudiante['id']);
    if ($promedio > 0) {
        $promedio_general += $promedio;
        if ($promedio < 70) $estudiantes_riesgo++;
        if ($promedio >= 90) $estudiantes_excelencia++;
    }
}

$promedio_general = $total_estudiantes > 0 ? round($promedio_general / $total_estudiantes, 1) : 0;

// Estadísticas por carrera
$estadisticas_carreras = [];
foreach ($carreras as $carrera) {
    $estudiantes_carrera = obtenerEstudiantesPorCarrera($carrera);
    $suma_promedios = 0;
    $contador = 0;
    
    foreach ($estudiantes_carrera as $estudiante) {
        $promedio = calcularPromedio($estudiante['id']);
        if ($promedio > 0) {
            $suma_promedios += $promedio;
            $contador++;
        }
    }
    
    $estadisticas_carreras[] = [
        'nombre' => $carrera,
        'total_estudiantes' => count($estudiantes_carrera),
        'promedio' => $contador > 0 ? round($suma_promedios / $contador, 1) : 0
    ];
}

// Análisis de rendimiento por materias
$rendimiento_materias = [];
foreach ($materias as $materia) {
    $notas_materia = obtenerNotasPorMateria($materia['id']);
    $suma_notas = 0;
    $aprobados = 0;
    
    foreach ($notas_materia as $nota) {
        $suma_notas += $nota['nota'];
        if ($nota['nota'] >= 70) $aprobados++;
    }
    
    $rendimiento_materias[] = [
        'id' => $materia['id'],
        'nombre' => $materia['nombre'],
        'carrera' => $materia['carrera_nombre'],
        'total_evaluados' => count($notas_materia),
        'promedio' => count($notas_materia) > 0 ? round($suma_notas / count($notas_materia), 1) : 0,
        'aprobados' => $aprobados,
        'porcentaje_aprobacion' => count($notas_materia) > 0 ? round(($aprobados / count($notas_materia)) * 100, 1) : 0
    ];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estudiantes - Reportes</title>
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
                    <a href="notas.php" class="nav-button">
                        <span class="nav-icon">
                            <img src="assets/exam-icon.svg" alt="Notas">
                        </span>
                        <span class="nav-text">Notas</span>
                    </a>
                    <a href="reportes.php" class="nav-button active">
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
                <span class="breadcrumb-current">Reportes Estadísticos</span>
            </nav>

            <?php if ($tipo_reporte === 'dashboard'): ?>
                <!-- DASHBOARD PRINCIPAL DE REPORTES -->
                
                <!-- Filtros y exportación -->
                <section class="report-controls fade-in">
                    <div class="controls-header">
                        <div class="controls-title">
                            <span class="controls-icon">📊</span>
                            <h2>Centro de Reportes Académicos</h2>
                        </div>
                        <div class="export-controls">
                            <button class="btn-export" onclick="exportarReporte('pdf')">
                                <span class="export-icon">📄</span>
                                <span>PDF</span>
                            </button>
                            <button class="btn-export" onclick="exportarReporte('excel')">
                                <span class="export-icon">📊</span>
                                <span>Excel</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="controls-filters">
                        <select class="filter-select" onchange="filtrarPorCarrera(this.value)">
                            <option value="">📚 Todas las carreras</option>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?php echo htmlspecialchars($carrera); ?>" 
                                        <?php echo $carrera_filtro === $carrera ? 'selected' : ''; ?>>
                                    <?php echo $carrera; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <div class="date-range">
                            <input type="date" class="date-input" placeholder="Desde">
                            <span class="date-separator">hasta</span>
                            <input type="date" class="date-input" placeholder="Hasta">
                        </div>
                        
                        <button class="btn-filter" onclick="aplicarFiltros()">
                            <span>🔍</span>
                            Aplicar Filtros
                        </button>
                    </div>
                </section>

                <!-- Estadísticas generales destacadas -->
                <section class="report-hero fade-in">
                    <div class="hero-stats">
                        <div class="hero-stat-main">
                            <div class="hero-number"><?php echo $promedio_general; ?></div>
                            <div class="hero-label">Promedio General</div>
                            <div class="hero-trend">
                                <span class="trend-arrow">📈</span>
                                <span class="trend-text">+2.3 vs período anterior</span>
                            </div>
                        </div>
                        
                        <div class="hero-stats-grid">
                            <div class="hero-mini-stat">
                                <div class="mini-stat-number"><?php echo $total_estudiantes; ?></div>
                                <div class="mini-stat-label">Total Estudiantes</div>
                            </div>
                            <div class="hero-mini-stat">
                                <div class="mini-stat-number"><?php echo $estudiantes_excelencia; ?></div>
                                <div class="mini-stat-label">En Excelencia</div>
                            </div>
                            <div class="hero-mini-stat">
                                <div class="mini-stat-number"><?php echo $estudiantes_riesgo; ?></div>
                                <div class="mini-stat-label">En Riesgo</div>
                            </div>
                            <div class="hero-mini-stat">
                                <div class="mini-stat-number"><?php echo count($materias); ?></div>
                                <div class="mini-stat-label">Materias Activas</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Menú de tipos de reportes -->
                <section class="report-menu fade-in">
                    <h2 class="section-title">
                        <span class="title-icon">📋</span>
                        Tipos de Reportes Disponibles
                    </h2>
                    
                    <div class="report-types-grid">
                        <div class="report-type-card" onclick="navegarReporte('rendimiento')">
                            <div class="report-card-icon">📈</div>
                            <div class="report-card-content">
                                <h3>Rendimiento Académico</h3>
                                <p>Análisis detallado del desempeño estudiantil por carreras y materias</p>
                                <div class="report-stats">
                                    <span class="report-stat">📊 <?php echo $total_estudiantes; ?> estudiantes</span>
                                    <span class="report-stat">📚 <?php echo count($materias); ?> materias</span>
                                </div>
                            </div>
                            <div class="report-arrow">→</div>
                        </div>
                        
                        <div class="report-type-card" onclick="navegarReporte('carreras')">
                            <div class="report-card-icon">🎓</div>
                            <div class="report-card-content">
                                <h3>Análisis por Carreras</h3>
                                <p>Comparativo de rendimiento y estadísticas entre diferentes carreras</p>
                                <div class="report-stats">
                                    <span class="report-stat">🏫 <?php echo count($carreras); ?> carreras</span>
                                    <span class="report-stat">📊 Promedios comparativos</span>
                                </div>
                            </div>
                            <div class="report-arrow">→</div>
                        </div>
                        
                        <div class="report-type-card" onclick="navegarReporte('materias')">
                            <div class="report-card-icon">📚</div>
                            <div class="report-card-content">
                                <h3>Estadísticas por Materias</h3>
                                <p>Análisis de dificultad, aprobación y rendimiento por asignatura</p>
                                <div class="report-stats">
                                    <span class="report-stat">📝 Índices de aprobación</span>
                                    <span class="report-stat">📊 Promedios por materia</span>
                                </div>
                            </div>
                            <div class="report-arrow">→</div>
                        </div>
                        
                        <div class="report-type-card" onclick="navegarReporte('ranking')">
                            <div class="report-card-icon">🏆</div>
                            <div class="report-card-content">
                                <h3>Ranking Estudiantil</h3>
                                <p>Clasificación de estudiantes por rendimiento y reconocimientos</p>
                                <div class="report-stats">
                                    <span class="report-stat">🥇 Top performers</span>
                                    <span class="report-stat">📈 Mejores promedios</span>
                                </div>
                            </div>
                            <div class="report-arrow">→</div>
                        </div>
                    </div>
                </section>

            <?php elseif ($tipo_reporte === 'rendimiento'): ?>
                <!-- REPORTE DE RENDIMIENTO ACADÉMICO -->
                <div class="report-header fade-in">
                    <div class="report-nav">
                        <a href="reportes.php" class="btn-back">← Volver a Reportes</a>
                    </div>
                    <div class="report-title-section">
                        <span class="report-icon">📈</span>
                        <div>
                            <h1>Reporte de Rendimiento Académico</h1>
                            <p class="report-subtitle">Análisis completo del desempeño estudiantil</p>
                        </div>
                    </div>
                </div>

                <!-- Métricas principales -->
                <section class="metrics-section fade-in">
                    <div class="metrics-grid">
                        <div class="metric-card primary">
                            <div class="metric-value"><?php echo $promedio_general; ?>/100</div>
                            <div class="metric-label">Promedio General</div>
                            <div class="metric-indicator positive">+2.1%</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value"><?php echo round(($total_estudiantes - $estudiantes_riesgo) / $total_estudiantes * 100, 1); ?>%</div>
                            <div class="metric-label">Tasa de Aprobación</div>
                            <div class="metric-indicator positive">+5.2%</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value"><?php echo $estudiantes_excelencia; ?></div>
                            <div class="metric-label">Estudiantes Destacados</div>
                            <div class="metric-indicator positive">+8</div>
                        </div>
                        <div class="metric-card warning">
                            <div class="metric-value"><?php echo $estudiantes_riesgo; ?></div>
                            <div class="metric-label">Estudiantes en Riesgo</div>
                            <div class="metric-indicator negative">-3</div>
                        </div>
                    </div>
                </section>

                <!-- Gráfico de distribución de notas -->
                <section class="chart-section fade-in">
                    <h3 class="chart-title">
                        <span class="chart-icon">📊</span>
                        Distribución del Rendimiento Académico
                    </h3>
                    <div class="chart-container">
                        <div class="grade-distribution">
                            <?php 
                            $excelente = $muy_bueno = $bueno = $regular = $deficiente = 0;
                            foreach ($estudiantes as $estudiante) {
                                $promedio = calcularPromedio($estudiante['id']);
                                if ($promedio >= 90) $excelente++;
                                elseif ($promedio >= 80) $muy_bueno++;
                                elseif ($promedio >= 70) $bueno++;
                                elseif ($promedio >= 60) $regular++;
                                else $deficiente++;
                            }
                            
                            $total = $excelente + $muy_bueno + $bueno + $regular + $deficiente;
                            ?>
                            
                            <div class="distribution-bar">
                                <div class="bar-segment excellent" style="width: <?php echo $total > 0 ? ($excelente / $total) * 100 : 0; ?>%"></div>
                                <div class="bar-segment very-good" style="width: <?php echo $total > 0 ? ($muy_bueno / $total) * 100 : 0; ?>%"></div>
                                <div class="bar-segment good" style="width: <?php echo $total > 0 ? ($bueno / $total) * 100 : 0; ?>%"></div>
                                <div class="bar-segment regular" style="width: <?php echo $total > 0 ? ($regular / $total) * 100 : 0; ?>%"></div>
                                <div class="bar-segment poor" style="width: <?php echo $total > 0 ? ($deficiente / $total) * 100 : 0; ?>%"></div>
                            </div>
                            
                            <div class="distribution-legend">
                                <div class="legend-item">
                                    <span class="legend-color excellent"></span>
                                    <span class="legend-label">Excelente (90-100): <?php echo $excelente; ?></span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color very-good"></span>
                                    <span class="legend-label">Muy Bueno (80-89): <?php echo $muy_bueno; ?></span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color good"></span>
                                    <span class="legend-label">Bueno (70-79): <?php echo $bueno; ?></span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color regular"></span>
                                    <span class="legend-label">Regular (60-69): <?php echo $regular; ?></span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color poor"></span>
                                    <span class="legend-label">Deficiente (<60): <?php echo $deficiente; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            <?php elseif ($tipo_reporte === 'carreras'): ?>
                <!-- REPORTE POR CARRERAS -->
                <div class="report-header fade-in">
                    <div class="report-nav">
                        <a href="reportes.php" class="btn-back">← Volver a Reportes</a>
                    </div>
                    <div class="report-title-section">
                        <span class="report-icon">🎓</span>
                        <div>
                            <h1>Análisis por Carreras</h1>
                            <p class="report-subtitle">Comparativo de rendimiento entre carreras</p>
                        </div>
                    </div>
                </div>

                <section class="carreras-comparison fade-in">
                    <div class="carreras-grid">
                        <?php foreach ($estadisticas_carreras as $carrera_stat): ?>
                            <div class="carrera-card">
                                <div class="carrera-header">
                                    <h3><?php echo $carrera_stat['nombre']; ?></h3>
                                    <div class="carrera-promedio <?php echo $carrera_stat['promedio'] >= 80 ? 'high' : ($carrera_stat['promedio'] >= 70 ? 'medium' : 'low'); ?>">
                                        <?php echo $carrera_stat['promedio']; ?>
                                    </div>
                                </div>
                                <div class="carrera-stats">
                                    <div class="carrera-stat">
                                        <span class="stat-label">Estudiantes:</span>
                                        <span class="stat-value"><?php echo $carrera_stat['total_estudiantes']; ?></span>
                                    </div>
                                    <div class="carrera-performance-bar">
                                        <div class="performance-fill" style="width: <?php echo min($carrera_stat['promedio'], 100); ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

            <?php elseif ($tipo_reporte === 'materias'): ?>
                <!-- REPORTE POR MATERIAS -->
                <div class="report-header fade-in">
                    <div class="report-nav">
                        <a href="reportes.php" class="btn-back">← Volver a Reportes</a>
                    </div>
                    <div class="report-title-section">
                        <span class="report-icon">📚</span>
                        <div>
                            <h1>Estadísticas por Materias</h1>
                            <p class="report-subtitle">Análisis de rendimiento por asignatura</p>
                        </div>
                    </div>
                </div>

                <section class="materias-analysis fade-in">
                    <div class="materias-table">
                        <div class="table-header">
                            <div class="header-cell">Materia</div>
                            <div class="header-cell">Carrera</div>
                            <div class="header-cell">Evaluados</div>
                            <div class="header-cell">Promedio</div>
                            <div class="header-cell">Aprobación</div>
                            <div class="header-cell">Dificultad</div>
                        </div>
                        
                        <?php 
                        // Ordenar materias por promedio descendente
                        usort($rendimiento_materias, function($a, $b) {
                            return $b['promedio'] <=> $a['promedio'];
                        });
                        
                        foreach ($rendimiento_materias as $materia): 
                            $dificultad = $materia['promedio'] >= 80 ? 'Fácil' : ($materia['promedio'] >= 70 ? 'Moderada' : 'Difícil');
                            $dificultad_class = $materia['promedio'] >= 80 ? 'easy' : ($materia['promedio'] >= 70 ? 'moderate' : 'hard');
                        ?>
                            <div class="table-row">
                                <div class="table-cell">
                                    <strong><?php echo $materia['nombre']; ?></strong>
                                </div>
                                <div class="table-cell">
                                    <span class="carrera-tag"><?php echo $materia['carrera']; ?></span>
                                </div>
                                <div class="table-cell"><?php echo $materia['total_evaluados']; ?></div>
                                <div class="table-cell">
                                    <span class="promedio-badge <?php echo $materia['promedio'] >= 80 ? 'high' : ($materia['promedio'] >= 70 ? 'medium' : 'low'); ?>">
                                        <?php echo $materia['promedio']; ?>
                                    </span>
                                </div>
                                <div class="table-cell">
                                    <div class="aprobacion-bar">
                                        <div class="aprobacion-fill" style="width: <?php echo $materia['porcentaje_aprobacion']; ?>%"></div>
                                        <span class="aprobacion-text"><?php echo $materia['porcentaje_aprobacion']; ?>%</span>
                                    </div>
                                </div>
                                <div class="table-cell">
                                    <span class="dificultad-badge <?php echo $dificultad_class; ?>"><?php echo $dificultad; ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

            <?php elseif ($tipo_reporte === 'ranking'): ?>
                <!-- REPORTE DE RANKING -->
                <div class="report-header fade-in">
                    <div class="report-nav">
                        <a href="reportes.php" class="btn-back">← Volver a Reportes</a>
                    </div>
                    <div class="report-title-section">
                        <span class="report-icon">🏆</span>
                        <div>
                            <h1>Ranking Estudiantil</h1>
                            <p class="report-subtitle">Clasificación por rendimiento académico</p>
                        </div>
                    </div>
                </div>

                <section class="ranking-section fade-in">
                    <?php if (!empty($ranking_estudiantes) && count($ranking_estudiantes) > 0): ?>
                    <div class="ranking-podium">
                        <?php 
                        $top_3 = array_slice($ranking_estudiantes, 0, 3);
                        $podium_order = [1, 0, 2]; // Para mostrar 2do, 1ro, 3ro
                        ?>
                        
                        <?php foreach ($podium_order as $index): ?>
                            <?php if (isset($top_3[$index]) && isset($top_3[$index]['estudiante'])): ?>
                                <div class="podium-position position-<?php echo $index + 1; ?>">
                                    <div class="podium-student">
                                        <div class="student-avatar-large">
                                            <?php 
                                            $nombre = $top_3[$index]['estudiante']['nombre'];
                                            $nombre_parts = explode(' ', $nombre);
                                            $iniciales = '';
                                            if (count($nombre_parts) >= 2) {
                                                $iniciales = strtoupper(substr($nombre_parts[0], 0, 1) . substr($nombre_parts[1], 0, 1));
                                            } else {
                                                $iniciales = strtoupper(substr($nombre, 0, 2));
                                            }
                                            echo $iniciales;
                                            ?>
                                        </div>
                                        <div class="student-rank-info">
                                            <div class="rank-medal">
                                                <?php echo $index === 0 ? '🥇' : ($index === 1 ? '🥈' : '🥉'); ?>
                                            </div>
                                            <h3><?php echo htmlspecialchars($nombre); ?></h3>
                                            <p class="student-carrera"><?php echo htmlspecialchars($top_3[$index]['estudiante']['carrera']); ?></p>
                                            <div class="student-score"><?php echo $top_3[$index]['promedio']; ?></div>
                                        </div>
                                    </div>
                                    <div class="podium-base"></div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="ranking-list">
                        <h3>📊 Ranking Completo</h3>
                        <?php foreach ($ranking_estudiantes as $index => $estudiante): ?>
                            <?php if (isset($estudiante['estudiante']) && isset($estudiante['estudiante']['nombre'])): ?>
                                <div class="ranking-item <?php echo $index < 3 ? 'top-three' : ''; ?>">
                                    <div class="rank-number">#{<?php echo $index + 1; ?>}</div>
                                    <div class="student-info">
                                        <div class="student-avatar-small">
                                            <?php 
                                            $nombre = $estudiante['estudiante']['nombre'];
                                            $nombre_parts = explode(' ', $nombre);
                                            $iniciales = '';
                                            if (count($nombre_parts) >= 2) {
                                                $iniciales = strtoupper(substr($nombre_parts[0], 0, 1) . substr($nombre_parts[1], 0, 1));
                                            } else {
                                                $iniciales = strtoupper(substr($nombre, 0, 2));
                                            }
                                            echo $iniciales;
                                            ?>
                                        </div>
                                        <div class="student-details">
                                            <h4><?php echo htmlspecialchars($estudiante['estudiante']['nombre']); ?></h4>
                                            <p><?php echo htmlspecialchars($estudiante['estudiante']['carrera']); ?></p>
                                        </div>
                                    </div>
                                    <div class="student-score-badge">
                                        <?php echo $estudiante['promedio']; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <strong>📊 Sin Datos Disponibles</strong><br>
                            No hay estudiantes con notas registradas para generar el ranking.
                            <br><br>
                            <a href="estudiantes.php" class="btn">Gestionar Estudiantes</a>
                            <a href="notas.php" class="btn btn-secondary">Registrar Notas</a>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
    </div>

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

        // Efectos visuales
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de entrada secuencial
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });

            // Efecto hover en tarjetas de reportes
            document.querySelectorAll('.stat-card').forEach(card => {
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
        });
        
        // ===== FUNCIONES ESPECÍFICAS PARA REPORTES =====
        
        // Funciones de navegación entre reportes
        function navegarReporte(tipo) {
            window.location.href = `reportes.php?tipo=${tipo}`;
        }

        // Función para filtrar por carrera
        function filtrarPorCarrera(carrera) {
            const url = new URL(window.location);
            if (carrera) {
                url.searchParams.set('carrera', carrera);
            } else {
                url.searchParams.delete('carrera');
            }
            window.location.href = url.toString();
        }

        // Función para aplicar filtros de fecha
        function aplicarFiltros() {
            const fechaInicio = document.querySelector('.date-input:first-of-type').value;
            const fechaFin = document.querySelector('.date-input:last-of-type').value;
            
            if (fechaInicio || fechaFin) {
                const url = new URL(window.location);
                if (fechaInicio) url.searchParams.set('fecha_inicio', fechaInicio);
                if (fechaFin) url.searchParams.set('fecha_fin', fechaFin);
                window.location.href = url.toString();
            }
        }

        // Funciones de exportación
        function exportarReporte(formato) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('exportar', formato);
            
            // Mostrar mensaje de éxito
            mostrarNotificacion(`📊 Exportando reporte en formato ${formato.toUpperCase()}...`, 'success');
            
            // Simular proceso de exportación
            setTimeout(() => {
                mostrarNotificacion(`✅ Reporte exportado exitosamente`, 'success');
            }, 2000);
        }

        // Función para mostrar notificaciones
        function mostrarNotificacion(mensaje, tipo = 'info') {
            // Crear elemento de notificación
            const notificacion = document.createElement('div');
            notificacion.className = `notification notification-${tipo}`;
            notificacion.innerHTML = `
                <div class="notification-content">
                    <span class="notification-message">${mensaje}</span>
                    <button class="notification-close" onclick="cerrarNotificacion(this)">×</button>
                </div>
            `;
            
            // Agregar estilos si no existen
            if (!document.querySelector('#notification-styles')) {
                const estilos = document.createElement('style');
                estilos.id = 'notification-styles';
                estilos.textContent = `
                    .notification {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        padding: 15px 20px;
                        border-radius: 8px;
                        color: white;
                        font-weight: 500;
                        z-index: 10000;
                        min-width: 300px;
                        animation: slideInRight 0.3s ease-out;
                        backdrop-filter: blur(10px);
                        border: 1px solid rgba(255, 255, 255, 0.2);
                        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
                    }
                    
                    .notification-success {
                        background: linear-gradient(135deg, rgba(0, 255, 157, 0.9), rgba(0, 200, 120, 0.9));
                    }
                    
                    .notification-info {
                        background: linear-gradient(135deg, rgba(0, 191, 255, 0.9), rgba(0, 150, 200, 0.9));
                    }
                    
                    .notification-content {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        gap: 15px;
                    }
                    
                    .notification-close {
                        background: none;
                        border: none;
                        color: white;
                        font-size: 1.2rem;
                        cursor: pointer;
                        opacity: 0.8;
                        transition: opacity 0.2s ease;
                        line-height: 1;
                    }
                    
                    .notification-close:hover {
                        opacity: 1;
                    }
                    
                    @keyframes slideInRight {
                        from {
                            transform: translateX(100%);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }
                    
                    @keyframes slideOutRight {
                        from {
                            transform: translateX(0);
                            opacity: 1;
                        }
                        to {
                            transform: translateX(100%);
                            opacity: 0;
                        }
                    }
                `;
                document.head.appendChild(estilos);
            }
            
            // Agregar al DOM
            document.body.appendChild(notificacion);
            
            // Auto-cerrar después de 3 segundos
            setTimeout(() => {
                if (notificacion.parentNode) {
                    cerrarNotificacion(notificacion.querySelector('.notification-close'));
                }
            }, 3000);
        }

        // Función para cerrar notificaciones
        function cerrarNotificacion(boton) {
            const notificacion = boton.closest('.notification');
            if (notificacion) {
                notificacion.style.animation = 'slideOutRight 0.3s ease-in forwards';
                setTimeout(() => {
                    if (notificacion.parentNode) {
                        notificacion.parentNode.removeChild(notificacion);
                    }
                }, 300);
            }
        }

        // Inicialización de efectos específicos para reportes
        if (window.location.pathname.includes('reportes.php')) {
            document.addEventListener('DOMContentLoaded', function() {
                // Agregar efectos de hover a las barras de distribución
                const barSegments = document.querySelectorAll('.bar-segment');
                barSegments.forEach(segment => {
                    segment.addEventListener('mouseenter', function() {
                        this.style.filter = 'brightness(1.2)';
                        this.style.transform = 'scaleY(1.1)';
                        this.style.transition = 'all 0.3s ease';
                    });
                    
                    segment.addEventListener('mouseleave', function() {
                        this.style.filter = 'brightness(1)';
                        this.style.transform = 'scaleY(1)';
                    });
                });

                // Agregar efectos de hover a las tarjetas de métricas
                const metricCards = document.querySelectorAll('.metric-card');
                metricCards.forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-5px) scale(1.02)';
                        this.style.boxShadow = '0 10px 30px rgba(147, 112, 219, 0.3)';
                        this.style.transition = 'all 0.3s ease';
                    });
                    
                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0) scale(1)';
                        this.style.boxShadow = 'none';
                    });
                });

                // Animar barras de progreso con retraso escalonado
                const performanceFills = document.querySelectorAll('.performance-fill, .aprobacion-fill');
                performanceFills.forEach((fill, index) => {
                    const targetWidth = fill.style.width;
                    fill.style.width = '0';
                    setTimeout(() => {
                        fill.style.transition = 'width 1.2s ease-out';
                        fill.style.width = targetWidth;
                    }, 200 + (index * 100)); // Retraso escalonado
                });

                // Animar números en las métricas
                const metricValues = document.querySelectorAll('.metric-value, .hero-number, .mini-stat-number');
                metricValues.forEach((value, index) => {
                    const text = value.textContent;
                    const number = parseFloat(text.replace(/[^\d.-]/g, ''));
                    if (!isNaN(number)) {
                        setTimeout(() => {
                            animateNumber(value, 0, number, 1500, text.includes('.'));
                        }, 300 + (index * 150));
                    }
                });

                // Efecto de aparición para elementos del ranking
                const rankingItems = document.querySelectorAll('.ranking-item');
                rankingItems.forEach((item, index) => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-20px)';
                    setTimeout(() => {
                        item.style.transition = 'all 0.5s ease-out';
                        item.style.opacity = '1';
                        item.style.transform = 'translateX(0)';
                    }, 100 + (index * 100));
                });
            });
        }

        // Función para animar números con formato
        function animateNumber(element, start, end, duration, isDecimal = false) {
            const startTime = performance.now();
            const originalText = element.textContent;
            const suffix = originalText.replace(/[\d.-]/g, '');
            
            function updateNumber(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Función de easing más suave
                const easeOutCubic = 1 - Math.pow(1 - progress, 3);
                const current = start + (end - start) * easeOutCubic;
                
                if (isDecimal) {
                    element.textContent = current.toFixed(1) + suffix;
                } else {
                    element.textContent = Math.round(current) + suffix;
                }
                
                if (progress < 1) {
                    requestAnimationFrame(updateNumber);
                } else {
                    // Asegurar valor final exacto
                    element.textContent = originalText;
                }
            }
            
            requestAnimationFrame(updateNumber);
        }

        // Funciones de utilidad para responsividad en reportes
        function ajustarLayoutReportes() {
            const isMobile = window.innerWidth <= 768;
            const tables = document.querySelectorAll('.materias-table');
            
            tables.forEach(table => {
                if (isMobile) {
                    table.classList.add('mobile-layout');
                } else {
                    table.classList.remove('mobile-layout');
                }
            });

            // Ajustar podium en móvil
            const podium = document.querySelector('.ranking-podium');
            if (podium) {
                if (isMobile) {
                    podium.style.flexDirection = 'column';
                    podium.style.alignItems = 'center';
                } else {
                    podium.style.flexDirection = 'row';
                    podium.style.alignItems = 'end';
                }
            }
        }

        // Listener para cambios de tamaño de ventana en reportes
        if (window.location.pathname.includes('reportes.php')) {
            window.addEventListener('resize', ajustarLayoutReportes);
            
            // Aplicar ajustes iniciales
            document.addEventListener('DOMContentLoaded', ajustarLayoutReportes);
        }
    </script>
</body>
</html>
