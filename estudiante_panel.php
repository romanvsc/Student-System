<?php
require_once 'auth.php';
require_once 'datos.php';

// Verificar que el usuario esté autenticado y sea alumno
if (!estaAutenticado() || !esAlumno()) {
    header('Location: login.php?error=sin_permisos');
    exit;
}

// Obtener datos del estudiante actual
$usuario_actual = obtenerUsuarioActual();
$estudiante_info = obtenerInformacionEstudianteActual();
$materias = obtenerMateriasEstudianteActual();
$notas = obtenerNotasEstudianteActual();
$promedio = obtenerPromedioEstudianteActual();

// Agrupar notas por materia
$notas_por_materia = [];
foreach ($notas as $nota) {
    $materia_id = $nota['materia_nombre'];
    if (!isset($notas_por_materia[$materia_id])) {
        $notas_por_materia[$materia_id] = [];
    }
    $notas_por_materia[$materia_id][] = $nota;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Estudiante - Student System</title>
    <link rel="stylesheet" href="estilos/style.css">
</head>
<body>
    <div class="container">
        <!-- Header específico para estudiantes -->
        <header class="header fade-in">
            <div class="header-content">
                <!-- Título principal -->
                <div class="header-brand">
                    <div class="brand-text">
                        <h1 class="titulo-principal neon-pulse">🎓 PANEL ESTUDIANTE</h1>
                        <span class="subtitle">Mi Portal Académico</span>
                    </div>
                </div>
                
                <!-- Navegación limitada para estudiantes -->
                <nav class="nav" id="main-nav">
                    <a href="estudiante_panel.php" class="nav-button active">
                        <span class="nav-icon">📚</span>
                        <span class="nav-text">Mis Materias</span>
                    </a>
                    <a href="estudiante_panel.php#notas" class="nav-button">
                        <span class="nav-icon">📝</span>
                        <span class="nav-text">Mis Notas</span>
                    </a>
                </nav>
                
                <!-- Información del usuario -->
                <div class="header-info">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo obtenerInicialesUsuario(); ?>
                        </div>
                        <div class="user-details">
                            <span class="user-name"><?php echo htmlspecialchars($usuario_actual['nombre_completo']); ?></span>
                            <span class="user-role">👨‍🎓 Estudiante</span>
                        </div>
                        <div class="user-actions">
                            <button class="user-menu-btn" onclick="toggleUserMenu()" title="Menú de usuario">
                                ⚙️
                            </button>
                        </div>
                    </div>
                    <div class="current-time" id="current-time"></div>
                </div>
            </div>
        </header>

        <!-- Menú de usuario limitado para estudiantes -->
        <div class="user-menu" id="user-menu">
            <a href="perfil.php" class="menu-item">
                <span>👤</span>
                <span>Mi Perfil</span>
            </a>
            <div class="menu-divider"></div>
            <a href="logout.php" class="menu-item logout">
                <span>🚪</span>
                <span>Cerrar Sesión</span>
            </a>
        </div>

        <div class="content-wrapper">
            <!-- Información del estudiante -->
            <?php if ($estudiante_info): ?>
            <section class="report-section fade-in">
                <h2 class="titulo-seccion">👤 MI INFORMACIÓN ACADÉMICA</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Carrera</div>
                        <div class="stat-number texto-cyber-blue" style="font-size: 1.2rem;">
                            <?php echo htmlspecialchars($estudiante_info['carrera_nombre']); ?>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Semestre Actual</div>
                        <div class="stat-number numero-destacado"><?php echo $estudiante_info['semestre']; ?>°</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Promedio General</div>
                        <div class="stat-number texto-neon-green"><?php echo $promedio; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Estado</div>
                        <div class="stat-number texto-cyber-blue" style="font-size: 1.2rem;">
                            <?php echo ucfirst($estudiante_info['estado']); ?>
                        </div>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- Mis Materias -->
            <section class="report-section fade-in">
                <h2 class="titulo-seccion">📚 MIS MATERIAS</h2>
                
                <?php if (!empty($materias)): ?>
                    <div class="students-grid">
                        <?php foreach ($materias as $materia): ?>
                            <div class="student-card">
                                <div class="student-header">
                                    <div class="student-avatar">📖</div>
                                    <div class="student-info">
                                        <h3><?php echo htmlspecialchars($materia['nombre']); ?></h3>
                                        <p class="carrera">Créditos: <?php echo $materia['creditos']; ?></p>
                                    </div>
                                </div>
                                
                                <div class="student-details">
                                    <?php if (!empty($materia['descripcion'])): ?>
                                        <p><strong>📋 Descripción:</strong> <?php echo htmlspecialchars($materia['descripcion']); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Calcular promedio de la materia
                                    $notas_materia = array_filter($notas, function($nota) use ($materia) {
                                        return $nota['materia_nombre'] === $materia['nombre'];
                                    });
                                    
                                    if (!empty($notas_materia)) {
                                        $suma = array_sum(array_column($notas_materia, 'nota'));
                                        $promedio_materia = round($suma / count($notas_materia), 1);
                                        $clase_promedio = $promedio_materia >= 70 ? 'promedio-bueno' : 'promedio-riesgo';
                                    ?>
                                        <div class="promedio-badge <?php echo $clase_promedio; ?>">
                                            Promedio: <?php echo $promedio_materia; ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="promedio-badge promedio-riesgo">
                                            Sin calificaciones
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <strong>📚 Sin materias asignadas</strong><br>
                        Aún no tienes materias asignadas o calificaciones registradas.
                    </div>
                <?php endif; ?>
            </section>

            <!-- Mis Notas -->
            <section class="report-section fade-in" id="notas">
                <h2 class="titulo-seccion">📝 MIS CALIFICACIONES</h2>
                
                <?php if (!empty($notas)): ?>
                    <?php foreach ($notas_por_materia as $materia_nombre => $notas_materia): ?>
                        <div class="report-section" style="margin-bottom: 30px;">
                            <h3 class="titulo-card">📖 <?php echo htmlspecialchars($materia_nombre); ?></h3>
                            
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Calificación</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($notas_materia as $nota): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y', strtotime($nota['fecha_evaluacion'])); ?></td>
                                                <td>
                                                    <span class="nota-badge <?php echo $nota['nota'] >= 70 ? 'nota-aprobado' : 'nota-desaprobado'; ?>">
                                                        <?php echo $nota['nota']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($nota['observaciones'] ?? 'Sin observaciones'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <strong>📝 Sin calificaciones</strong><br>
                        Aún no tienes calificaciones registradas en el sistema.
                    </div>
                <?php endif; ?>
            </section>

            <!-- Análisis de rendimiento -->
            <?php if (!empty($notas)): ?>
            <section class="report-section fade-in">
                <h2 class="titulo-seccion">📊 MI RENDIMIENTO ACADÉMICO</h2>
                
                <?php
                $materias_aprobadas = 0;
                $materias_reprobadas = 0;
                $total_materias = count($materias);
                
                foreach ($materias as $materia) {
                    $notas_materia = array_filter($notas, function($nota) use ($materia) {
                        return $nota['materia_nombre'] === $materia['nombre'];
                    });
                    
                    if (!empty($notas_materia)) {
                        $suma = array_sum(array_column($notas_materia, 'nota'));
                        $promedio_materia = $suma / count($notas_materia);
                        
                        if ($promedio_materia >= 70) {
                            $materias_aprobadas++;
                        } else {
                            $materias_reprobadas++;
                        }
                    }
                }
                ?>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number texto-neon-green"><?php echo $materias_aprobadas; ?></div>
                        <div class="stat-label">Materias Aprobadas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number texto-neon-pink"><?php echo $materias_reprobadas; ?></div>
                        <div class="stat-label">Materias en Riesgo</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number texto-cyber-blue"><?php echo count($notas); ?></div>
                        <div class="stat-label">Total Calificaciones</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" style="color: var(--sunset-orange);">
                            <?php echo obtenerEstadoAcademico($promedio); ?>
                        </div>
                        <div class="stat-label">Estado Académico</div>
                    </div>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript -->
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

        setInterval(updateTime, 1000);
        updateTime();

        // Toggle del menú de usuario
        function toggleUserMenu() {
            const userMenu = document.getElementById('user-menu');
            const menuBtn = document.querySelector('.user-menu-btn');
            
            if (!userMenu || !menuBtn) return;

            if (userMenu.classList.contains('active')) {
                userMenu.classList.remove('active');
            } else {
                // Posicionar el menú fijo en la parte superior derecha
                userMenu.style.top = '80px';
                userMenu.style.right = '20px';
                userMenu.classList.add('active');
            }
        }

        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('user-menu');
            const button = document.querySelector('.user-menu-btn');
            
            if (menu && !menu.contains(event.target) && button && !button.contains(event.target)) {
                menu.classList.remove('active');
            }
        });

        // Efectos visuales
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de entrada secuencial
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });

            // Smooth scroll para navegación interna
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>