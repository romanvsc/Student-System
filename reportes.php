<?php
require_once 'datos.php';

// En desarrollo - Sistema de reportes
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
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="estudiantes.php" class="nav-button">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">Estudiantes</span>
                    </a>
                    <a href="notas.php" class="nav-button">
                        <span class="nav-icon">�</span>
                        <span class="nav-text">Notas</span>
                    </a>
                    <a href="reportes.php" class="nav-button active">
                        <span class="nav-icon">�</span>
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
                <a href="index.php" class="breadcrumb-item">🏠 Inicio</a>
                <span class="breadcrumb-separator">›</span>
                <span class="breadcrumb-current">Reportes Estadísticos</span>
            </nav>

        <!-- Contenido principal -->
        <section class="report-section fade-in">
            <h2 class="titulo-seccion">📈 REPORTES ESTADÍSTICOS</h2>
            
            <div class="alert alert-info">
                <strong>🚧 En Desarrollo</strong><br>
                El módulo de reportes estadísticos está actualmente en desarrollo.
                <br><br>
                <strong>Reportes disponibles próximamente:</strong>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>✅ Reportes de rendimiento académico</li>
                    <li>✅ Estadísticas por carrera</li>
                    <li>✅ Análisis de tendencias</li>
                    <li>✅ Reportes de asistencia</li>
                    <li>✅ Gráficos interactivos</li>
                    <li>✅ Exportación a PDF/Excel</li>
                </ul>
                <br>
                <a href="index.php" class="btn">← Volver al Dashboard</a>
                <a href="estudiantes.php" class="btn btn-secondary">Ver Estudiantes</a>
            </div>
        </section>

        <!-- Vista previa de reportes -->
        <section class="report-section fade-in">
            <h2 class="titulo-seccion">📋 VISTA PREVIA DE REPORTES</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number texto-cyber-blue">📊</div>
                    <div class="stat-label">Reporte de Rendimiento</div>
                    <small class="texto-pequeño">Análisis detallado del rendimiento académico por estudiante y carrera</small>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number texto-neon-green">📈</div>
                    <div class="stat-label">Tendencias Académicas</div>
                    <small class="texto-pequeño">Evolución del rendimiento a lo largo del tiempo</small>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number texto-neon-pink">📋</div>
                    <div class="stat-label">Reportes Personalizados</div>
                    <small class="texto-pequeño">Generación de reportes según criterios específicos</small>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number" style="color: var(--sunset-orange);">📄</div>
                    <div class="stat-label">Exportación</div>
                    <small class="texto-pequeño">Descarga en formatos PDF, Excel y CSV</small>
                </div>
            </div>
        </section>
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
    </script>
</body>
</html>
