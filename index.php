<?php
require_once 'auth.php';
// De momento no requerimos autenticación obligatoria, pero preparamos el sistema
// requiereAutenticacion(); // Descomenta cuando quieras requerir login

// Redirigir estudiantes a su panel específico
if (estaAutenticado() && esAlumno()) {
    header('Location: estudiante_panel.php');
    exit;
}

// Obtener usuario actual si está autenticado
$usuario_actual = obtenerUsuarioActual();

require_once 'datos.php';


// ingresar con admin123

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
                    <a href="index.php" class="nav-button active">
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
                    <a href="reportes.php" class="nav-button">
                        <span class="nav-icon">
                            <img src="assets/report.svg" alt="Reportes">
                        </span>
                        <span class="nav-text">Reportes</span>
                    </a>
                </nav>
                
                <!-- Información del usuario/sistema -->
                <div class="header-info">
                    <?php if (estaAutenticado()): ?>
                        <!-- Usuario autenticado -->
                        <div class="user-info">
                            <div class="user-avatar">
                                <?php echo obtenerInicialesUsuario(); ?>
                            </div>
                            <div class="user-details">
                                <span class="user-name"><?php echo htmlspecialchars($usuario_actual['nombre_completo']); ?></span>
                                <span class="user-role">
                                    <?php echo $usuario_actual['tipo_usuario'] === 'administrador' ? '👑 Admin' : '👨‍🎓 Alumno'; ?>
                                </span>
                            </div>
                            <div class="user-actions">
                                <button class="user-menu-btn" onclick="toggleUserMenu()" title="Menú de usuario">
                                    ⚙️
                                </button>
                                <!-- ¡EL MENÚ SE HA MOVIDO! Solo queda el botón aquí -->
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Usuario no autenticado -->
                        <div class="system-status">
                            <span class="status-indicator guest"></span>
                            <span class="status-text">Invitado</span>
                        </div>
                        <a href="login.php" class="login-btn">
                            🔒 Iniciar Sesión
                        </a>
                    <?php endif; ?>
                    
                    <div class="current-time" id="current-time"></div>
                </div>
            </div>
            
            <!-- Botón menú móvil -->
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
        </header>

        <!-- 🚀 MENÚ FLOTANTE (fuera del header) -->
        <?php if (estaAutenticado()): ?>
        <div class="user-menu" id="user-menu">
            <a href="perfil.php" class="menu-item">
                <span>👤</span>
                <span>Mi Perfil</span>
            </a>
            <?php if (esAdministrador()): ?>
                <a href="usuarios.php" class="menu-item">
                    <span>👥</span>
                    <span>Gestión de Usuarios</span>
                </a>
            <?php endif; ?>
            <div class="menu-divider"></div>
            <a href="logout.php" class="menu-item logout">
                <span>🚪</span>
                <span>Cerrar Sesión</span>
            </a>
        </div>
        <?php endif; ?>

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
                <span class="breadcrumb-current">Dashboard</span>
            </nav>

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
            </div>
        </div>
    </div>

    <!-- JavaScript para efectos adicionales -->
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

        // Toggle del menú de usuario (mejorado con posicionamiento dinámico)
        function toggleUserMenu() {
            const menuBtn = document.querySelector('.user-menu-btn');
            const userMenu = document.getElementById('user-menu');
            
            if (!userMenu || !menuBtn) return;

            if (userMenu.classList.contains('active')) {
                userMenu.classList.remove('active');
                // Remover overlay
                const overlay = document.querySelector('.menu-overlay');
                if (overlay) {
                    overlay.remove();
                }
            } else {
                // Crear overlay
                const overlay = document.createElement('div');
                overlay.className = 'menu-overlay';
                overlay.style.cssText = `
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    width: 100vw !important;
                    height: 100vh !important;
                    z-index: 2147483646 !important;
                    pointer-events: none !important;
                `;
                document.body.appendChild(overlay);
                
                // Posicionar el menú fijo en la parte superior derecha
                const rect = menuBtn.getBoundingClientRect();
                userMenu.style.top = '80px'; // Posición fija desde arriba
                userMenu.style.right = '20px'; // Posición fija desde la derecha
                
                userMenu.classList.add('active');
            }
        }

        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('user-menu');
            const button = document.querySelector('.user-menu-btn');
            
            if (menu && !menu.contains(event.target) && button && !button.contains(event.target)) {
                menu.classList.remove('active');
                // Remover overlay
                const overlay = document.querySelector('.menu-overlay');
                if (overlay) {
                    overlay.remove();
                }
            }
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

        // Función para mostrar notificaciones
        function mostrarNotificacion(mensaje, tipo = 'info') {
            const notificacion = document.createElement('div');
            notificacion.className = `notification notification-${tipo}`;
            notificacion.textContent = mensaje;
            
            document.body.appendChild(notificacion);
            
            setTimeout(() => {
                notificacion.style.transform = 'translateX(100%)';
                notificacion.style.opacity = '0';
                
                setTimeout(() => {
                    document.body.removeChild(notificacion);
                }, 300);
            }, 3000);
        }

        // Mostrar mensaje de bienvenida si hay usuario autenticado
        <?php if (estaAutenticado()): ?>
            setTimeout(() => {
                mostrarNotificacion('¡Bienvenido de vuelta, <?php echo $usuario_actual['nombre_completo']; ?>!', 'success');
            }, 1000);
        <?php endif; ?>

        // Verificar errores en URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('error') === 'sin_permisos') {
            mostrarNotificacion('No tienes permisos para acceder a esa sección', 'error');
        }
    </script>
</body>
</html>