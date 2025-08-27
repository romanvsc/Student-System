<?php
require_once 'datos.php';

// Obtener estadísticas generales
$stats = obtenerEstadisticasGenerales();
$ranking = obtenerRankingEstudiantes();

// Top 3 estudiantes
$top_estudiantes = array_slice($ranking, 0, 3);

// Estudiantes en riesgo (promedio < 70)
$estudiantes_riesgo = array_filter($ranking, function($item) {
    return $item['promedio'] < 70;
});

// Distribución por carreras
$distribucion_carreras = [];
foreach ($stats['carreras'] as $carrera) {
    $estudiantes_carrera = obtenerEstudiantesPorCarrera($carrera);
    $distribucion_carreras[$carrera] = count($estudiantes_carrera);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estudiantes - Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Header con navegación -->
        <header class="header fade-in">
            <h1 class="titulo-principal neon-pulse">
                STUDENT SYSTEM
            </h1>
            <nav class="nav">
                <a href="index.php" class="nav-button active">Dashboard</a>
                <a href="estudiantes.php" class="nav-button">Estudiantes</a>
                <a href="notas.php" class="nav-button">Notas</a>
                <a href="reportes.php" class="nav-button">Reportes</a>
            </nav>
        </header>

        <!-- Estadísticas principales -->
        <section class="stats-grid fade-in">
            <div class="stat-card">
                <div class="stat-number numero-destacado"><?php echo $stats['total_estudiantes']; ?></div>
                <div class="stat-label">Total Estudiantes</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number numero-destacado"><?php echo $stats['total_carreras']; ?></div>
                <div class="stat-label">Carreras Activas</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number numero-destacado"><?php echo $stats['promedio_general']; ?></div>
                <div class="stat-label">Promedio General</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number numero-destacado"><?php echo count($estudiantes_riesgo); ?></div>
                <div class="stat-label">En Riesgo Académico</div>
            </div>
        </section>

        <!-- Alertas de estudiantes en riesgo -->
        <?php if (!empty($estudiantes_riesgo)): ?>
        <div class="alert alert-danger fade-in">
            <strong>⚠️ ALERTA ACADÉMICA:</strong> 
            <?php echo count($estudiantes_riesgo); ?> estudiante(s) necesitan atención inmediata.
        </div>
        <?php endif; ?>

        <!-- Top estudiantes -->
        <section class="report-section fade-in">
            <h2 class="titulo-seccion">
                <img src="assets/trofeo.svg" alt="Trofeo" class="section-icon">
                TOP ESTUDIANTES
            </h2>
            <div class="students-grid">
                <?php foreach ($top_estudiantes as $index => $item): ?>
                    <?php 
                    $estudiante = $item['estudiante'];
                    $promedio = $item['promedio'];
                    $estado = $item['estado_academico'];
                    $iniciales = strtoupper(substr($estudiante['nombre'], 0, 1) . substr(strstr($estudiante['nombre'], ' '), 1, 1));
                    
                    // Determinar clase de promedio
                    $clase_promedio = 'promedio-regular';
                    if ($promedio >= 90) $clase_promedio = 'promedio-excelente';
                    elseif ($promedio >= 80) $clase_promedio = 'promedio-muy-bueno';
                    elseif ($promedio >= 70) $clase_promedio = 'promedio-bueno';
                    else $clase_promedio = 'promedio-riesgo';
                    
                    // SVGs de medallas para top 3
                    $medallas_svg = [
                        'assets/medalla-oro.svg',
                        'assets/medalla-plata.svg', 
                        'assets/medalla-bronce.svg'
                    ];
                    $medalla_svg = isset($medallas_svg[$index]) ? $medallas_svg[$index] : '';
                    $posicion = $index + 1;
                    ?>
                    
                    <div class="student-card medal-card">
                        <?php if ($medalla_svg): ?>
                            <div class="medal-container">
                                <img src="<?php echo $medalla_svg; ?>" alt="Medalla <?php echo $posicion; ?>" class="medal-svg">
                                <span class="medal-position"><?php echo $posicion; ?>°</span>
                            </div>
                        <?php endif; ?>
                        
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
                            
                            <div class="promedio-badge <?php echo $clase_promedio; ?>">
                                Promedio: <?php echo $promedio; ?> - <?php echo $estado; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Distribución por carreras -->
        <section class="report-section fade-in">
            <h2 class="titulo-seccion">📊 DISTRIBUCIÓN POR CARRERAS</h2>
            <div class="stats-grid">
                <?php foreach ($distribucion_carreras as $carrera => $cantidad): ?>
                    <?php 
                    $porcentaje = round(($cantidad / $stats['total_estudiantes']) * 100, 1);
                    ?>
                    <div class="stat-card">
                        <div class="stat-number texto-cyber-blue"><?php echo $cantidad; ?></div>
                        <div class="stat-label"><?php echo $carrera; ?></div>
                        <div class="progress-bar mt-10">
                            <div class="progress-fill" style="width: <?php echo $porcentaje; ?>%"></div>
                        </div>
                        <small class="texto-pequeño"><?php echo $porcentaje; ?>% del total</small>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Análisis académico rápido -->
        <section class="report-section fade-in">
            <h2 class="titulo-seccion">📈 ANÁLISIS ACADÉMICO</h2>
            <div class="stats-grid">
                <?php
                $excelentes = count(array_filter($ranking, function($item) { return $item['promedio'] >= 90; }));
                $buenos = count(array_filter($ranking, function($item) { return $item['promedio'] >= 80 && $item['promedio'] < 90; }));
                $regulares = count(array_filter($ranking, function($item) { return $item['promedio'] >= 70 && $item['promedio'] < 80; }));
                $riesgo = count(array_filter($ranking, function($item) { return $item['promedio'] < 70; }));
                ?>
                
                <div class="stat-card">
                    <div class="stat-number texto-neon-green"><?php echo $excelentes; ?></div>
                    <div class="stat-label">Estudiantes Excelentes</div>
                    <small class="texto-pequeño">Promedio ≥ 90</small>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number texto-cyber-blue"><?php echo $buenos; ?></div>
                    <div class="stat-label">Estudiantes Buenos</div>
                    <small class="texto-pequeño">Promedio 80-89</small>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number" style="color: var(--sunset-orange);"><?php echo $regulares; ?></div>
                    <div class="stat-label">Estudiantes Regulares</div>
                    <small class="texto-pequeño">Promedio 70-79</small>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number texto-neon-pink"><?php echo $riesgo; ?></div>
                    <div class="stat-label">En Riesgo Académico</div>
                    <small class="texto-pequeño">Promedio < 70</small>
                </div>
            </div>
        </section>

        <!-- Panel de acciones rápidas -->
        <section class="report-section fade-in">
            <h2 class="titulo-seccion">⚡ ACCIONES RÁPIDAS</h2>
            <div class="flex-center" style="gap: 20px; flex-wrap: wrap;">
                <a href="estudiantes.php" class="btn">
                    👥 Gestionar Estudiantes
                </a>
                <a href="notas.php" class="btn btn-secondary">
                    📝 Administrar Notas
                </a>
                <a href="reportes.php" class="btn">
                    📊 Ver Reportes Completos
                </a>
            </div>
        </section>

        <!-- Información del sistema -->
        <section class="report-section fade-in">
            <h2 class="titulo-seccion">ℹ️ INFORMACIÓN DEL SISTEMA</h2>
            <div class="texto-normal">
                <p><strong>🕒 Última actualización:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                <p><strong>💾 Base de datos:</strong> MySQL (student_system)</p>
                <p><strong>🎯 Estado del sistema:</strong> 
                    <span class="texto-neon-green">✅ Operativo</span>
                </p>
                <p><strong>📋 Funcionalidades activas:</strong></p>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>✅ Gestión de estudiantes</li>
                    <li>✅ Sistema de notas</li>
                    <li>✅ Reportes estadísticos</li>
                    <li>✅ Dashboard en tiempo real</li>
                </ul>
            </div>
        </section>
    </div>

    <!-- JavaScript para efectos adicionales -->
    <script>
        // Animación de entrada secuencial
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
        });

        // Efecto de hover en las tarjetas de estadísticas
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Efecto especial para las medallas
        document.querySelectorAll('.medal-svg').forEach(medal => {
            medal.addEventListener('mouseenter', function() {
                this.style.transform = 'rotate(10deg) scale(1.1)';
                this.style.filter = 'drop-shadow(0 0 10px rgba(255, 215, 0, 0.7))';
            });
            
            medal.addEventListener('mouseleave', function() {
                this.style.transform = 'rotate(0deg) scale(1)';
                this.style.filter = 'none';
            });
        });

        // Actualizar números con animación
        function animateNumber(element, target) {
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 20);
        }

        // Animar números al cargar
        setTimeout(() => {
            document.querySelectorAll('.stat-number').forEach(el => {
                const target = parseInt(el.textContent);
                if (!isNaN(target)) {
                    el.textContent = '0';
                    animateNumber(el, target);
                }
            });
        }, 500);
    </script>
</body>
</html>