<?php
require_once 'auth.php';
require_once 'datos.php';

// Verificar que el usuario esté autenticado
if (!estaAutenticado()) {
    header('Location: login.php?error=no_auth');
    exit;
}

$usuario_actual = obtenerUsuarioActual();
$es_estudiante = esAlumno();

if ($es_estudiante) {
    $estudiante_info = obtenerInformacionEstudianteActual();
}

$mensaje = '';
$tipo_mensaje = '';

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_password'])) {
    $password_actual = $_POST['password_actual'] ?? '';
    $password_nueva = $_POST['password_nueva'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';
    
    if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
        $mensaje = 'Todos los campos son obligatorios.';
        $tipo_mensaje = 'error';
    } elseif ($password_nueva !== $password_confirmar) {
        $mensaje = 'Las contraseñas nuevas no coinciden.';
        $tipo_mensaje = 'error';
    } elseif (strlen($password_nueva) < 6) {
        $mensaje = 'La nueva contraseña debe tener al menos 6 caracteres.';
        $tipo_mensaje = 'error';
    } else {
        // Verificar contraseña actual
        if (password_verify($password_actual, $usuario_actual['password_hash'])) {
            // Actualizar contraseña
            global $mysqli;
            $nuevo_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
            $query = "UPDATE usuarios SET password_hash = ? WHERE id = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('si', $nuevo_hash, $usuario_actual['id']);
            
            if ($stmt->execute()) {
                $mensaje = 'Contraseña actualizada exitosamente.';
                $tipo_mensaje = 'exito';
            } else {
                $mensaje = 'Error al actualizar la contraseña.';
                $tipo_mensaje = 'error';
            }
        } else {
            $mensaje = 'La contraseña actual es incorrecta.';
            $tipo_mensaje = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Student System</title>
    <link rel="stylesheet" href="estilos/style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header fade-in">
            <div class="header-content">
                <div class="header-brand">
                    <div class="brand-text">
                        <h1 class="titulo-principal neon-pulse">👤 MI PERFIL</h1>
                        <span class="subtitle">Información Personal</span>
                    </div>
                </div>
                
                <nav class="nav">
                    <?php if ($es_estudiante): ?>
                        <a href="estudiante_panel.php" class="nav-button">
                            <span class="nav-icon">📚</span>
                            <span class="nav-text">Mi Panel</span>
                        </a>
                    <?php else: ?>
                        <a href="index.php" class="nav-button">
                            <span class="nav-icon">🏠</span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    <?php endif; ?>
                </nav>
                
                <div class="header-info">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo obtenerInicialesUsuario(); ?>
                        </div>
                        <div class="user-details">
                            <span class="user-name"><?php echo htmlspecialchars($usuario_actual['nombre_completo']); ?></span>
                            <span class="user-role">
                                <?php echo $usuario_actual['tipo_usuario'] === 'administrador' ? '👑 Admin' : '👨‍🎓 Estudiante'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
            <!-- Mensajes -->
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> fade-in">
                    <strong><?php echo $tipo_mensaje === 'exito' ? '✅' : '❌'; ?></strong> 
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <!-- Información del perfil -->
            <section class="report-section fade-in">
                <h2 class="titulo-seccion">� INFORMACIÓN PERSONAL</h2>
                
                <!-- Perfil principal -->
                <div class="profile-header" style="background: rgba(10, 10, 26, 0.8); border-radius: 15px; padding: 30px; margin-bottom: 30px; border: 1px solid rgba(0, 212, 255, 0.2);">
                    <div style="display: flex; align-items: center; gap: 30px; flex-wrap: wrap;">
                        <div class="profile-avatar" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--cyber-blue), var(--neon-green)); display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: bold; color: #000; box-shadow: 0 0 30px rgba(0, 212, 255, 0.3);">
                            <?php echo obtenerInicialesUsuario(); ?>
                        </div>
                        <div class="profile-info" style="flex: 1;">
                            <h1 style="font-size: 2.5rem; margin: 0 0 10px 0; background: linear-gradient(135deg, var(--cyber-blue), var(--neon-green)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                <?php echo htmlspecialchars($usuario_actual['nombre_completo']); ?>
                            </h1>
                            <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 15px;">
                                <div class="profile-badge" style="background: rgba(0, 212, 255, 0.1); border: 1px solid var(--cyber-blue); border-radius: 25px; padding: 8px 16px; display: inline-flex; align-items: center; gap: 8px;">
                                    <span style="color: var(--cyber-blue);">👑</span>
                                    <span style="color: var(--cyber-blue); font-weight: 600;">
                                        <?php echo $usuario_actual['tipo_usuario'] === 'administrador' ? 'Administrador del Sistema' : 'Estudiante'; ?>
                                    </span>
                                </div>
                                <div class="profile-badge" style="background: rgba(0, 255, 136, 0.1); border: 1px solid var(--neon-green); border-radius: 25px; padding: 8px 16px; display: inline-flex; align-items: center; gap: 8px;">
                                    <span style="color: var(--neon-green);">✅</span>
                                    <span style="color: var(--neon-green); font-weight: 600;">Cuenta Activa</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalles del perfil -->
                <div class="profile-details" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="detail-card" style="background: rgba(10, 10, 26, 0.6); border-radius: 12px; padding: 25px; border: 1px solid rgba(255, 255, 255, 0.1);">
                        <h3 style="color: var(--cyber-blue); margin: 0 0 15px 0; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                            <span>👤</span> Datos de Acceso
                        </h3>
                        <div>
                            <div style="margin-bottom: 15px;">
                                <label style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; display: block; margin-bottom: 5px;">Usuario</label>
                                <div style="color: var(--neon-green); font-weight: 600; font-size: 1.1rem;">
                                    <?php echo htmlspecialchars($usuario_actual['username']); ?>
                                </div>
                            </div>
                            <div>
                                <label style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; display: block; margin-bottom: 5px;">Correo Electrónico</label>
                                <div style="color: var(--cyber-blue); font-weight: 600; font-size: 1.1rem;">
                                    <?php echo htmlspecialchars($usuario_actual['email']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-card" style="background: rgba(10, 10, 26, 0.6); border-radius: 12px; padding: 25px; border: 1px solid rgba(255, 255, 255, 0.1);">
                        <h3 style="color: var(--sunset-orange); margin: 0 0 15px 0; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                            <span>🔒</span> Seguridad
                        </h3>
                        <div>
                            <div style="margin-bottom: 15px;">
                                <label style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; display: block; margin-bottom: 5px;">Última Sesión</label>
                                <div style="color: var(--neon-green); font-weight: 600; font-size: 1.1rem;">
                                    Activa
                                </div>
                            </div>
                            <div>
                                <label style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; display: block; margin-bottom: 5px;">Estado de Seguridad</label>
                                <div style="color: var(--cyber-blue); font-weight: 600; font-size: 1.1rem;">
                                    Protegida
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($es_estudiante && $estudiante_info): ?>
                    <h3 class="titulo-card" style="margin-top: 30px;">🎓 INFORMACIÓN ACADÉMICA</h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-label">Carrera</div>
                            <div class="stat-number texto-cyber-blue" style="font-size: 1.2rem;">
                                <?php echo htmlspecialchars($estudiante_info['carrera_nombre']); ?>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Semestre</div>
                            <div class="stat-number numero-destacado"><?php echo $estudiante_info['semestre']; ?>°</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Estado</div>
                            <div class="stat-number texto-neon-green" style="font-size: 1.2rem;">
                                <?php echo ucfirst($estudiante_info['estado']); ?>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-label">Fecha de Ingreso</div>
                            <div class="stat-number texto-cyber-blue" style="font-size: 1.2rem;">
                                <?php echo date('d/m/Y', strtotime($estudiante_info['fecha_ingreso'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Cambio de contraseña -->
            <section class="report-section fade-in">
                <h2 class="titulo-seccion">� SEGURIDAD DE LA CUENTA</h2>
                
                <div style="background: rgba(10, 10, 26, 0.8); border-radius: 15px; padding: 30px; border: 1px solid rgba(255, 107, 157, 0.2);">
                    <div style="margin-bottom: 25px;">
                        <h3 style="color: var(--neon-pink); margin: 0 0 10px 0; font-size: 1.3rem; display: flex; align-items: center; gap: 10px;">
                            <span>🔒</span> Cambiar Contraseña
                        </h3>
                        <p style="color: rgba(255, 255, 255, 0.7); margin: 0; font-size: 0.95rem;">
                            Mantén tu cuenta segura actualizando tu contraseña regularmente. 
                            La nueva contraseña debe tener al menos 6 caracteres.
                        </p>
                    </div>

                    <form method="POST" class="password-form">
                        <input type="hidden" name="cambiar_password" value="1">
                        
                        <div style="display: grid; gap: 20px; max-width: 500px;">
                            <div class="form-group">
                                <label for="password_actual" style="color: var(--cyber-blue); font-weight: 600; margin-bottom: 8px; display: block;">
                                    Contraseña Actual *
                                </label>
                                <input type="password" 
                                       id="password_actual" 
                                       name="password_actual" 
                                       required 
                                       class="form-input"
                                       style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px; padding: 12px 15px; color: white; width: 100%; transition: all 0.3s ease;"
                                       placeholder="Ingresa tu contraseña actual">
                            </div>
                            
                            <div class="form-group">
                                <label for="password_nueva" style="color: var(--neon-green); font-weight: 600; margin-bottom: 8px; display: block;">
                                    Nueva Contraseña *
                                </label>
                                <input type="password" 
                                       id="password_nueva" 
                                       name="password_nueva" 
                                       required 
                                       class="form-input" 
                                       minlength="6"
                                       style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px; padding: 12px 15px; color: white; width: 100%; transition: all 0.3s ease;"
                                       placeholder="Mínimo 6 caracteres">
                            </div>
                            
                            <div class="form-group">
                                <label for="password_confirmar" style="color: var(--sunset-orange); font-weight: 600; margin-bottom: 8px; display: block;">
                                    Confirmar Nueva Contraseña *
                                </label>
                                <input type="password" 
                                       id="password_confirmar" 
                                       name="password_confirmar" 
                                       required 
                                       class="form-input" 
                                       minlength="6"
                                       style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 8px; padding: 12px 15px; color: white; width: 100%; transition: all 0.3s ease;"
                                       placeholder="Repite la nueva contraseña">
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px; display: flex; gap: 15px; flex-wrap: wrap;">
                            <button type="submit" 
                                    class="btn-primary" 
                                    style="background: linear-gradient(135deg, var(--neon-pink), #ff6b9d); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;">
                                <span>�</span> Actualizar Contraseña
                            </button>
                            <?php if ($es_estudiante): ?>
                                <a href="estudiante_panel.php" 
                                   class="btn-secondary" 
                                   style="background: rgba(255, 255, 255, 0.1); color: var(--cyber-blue); text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;">
                                    <span>←</span> Volver al Panel
                                </a>
                            <?php else: ?>
                                <a href="index.php" 
                                   class="btn-secondary" 
                                   style="background: rgba(255, 255, 255, 0.1); color: var(--cyber-blue); text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;">
                                    <span>←</span> Volver al Dashboard
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <script>
        // Validación de formulario mejorada
        document.querySelector('.password-form').addEventListener('submit', function(e) {
            const passwordNueva = document.getElementById('password_nueva').value;
            const passwordConfirmar = document.getElementById('password_confirmar').value;
            
            if (passwordNueva !== passwordConfirmar) {
                e.preventDefault();
                mostrarNotificacion('Las contraseñas nuevas no coinciden.', 'error');
                return false;
            }
            
            if (passwordNueva.length < 6) {
                e.preventDefault();
                mostrarNotificacion('La nueva contraseña debe tener al menos 6 caracteres.', 'error');
                return false;
            }
        });

        // Efectos en inputs
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de entrada secuencial
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });

            // Efectos en inputs de contraseña
            const inputs = document.querySelectorAll('input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.borderColor = 'var(--cyber-blue)';
                    this.style.boxShadow = '0 0 10px rgba(0, 212, 255, 0.3)';
                });
                
                input.addEventListener('blur', function() {
                    this.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                    this.style.boxShadow = 'none';
                });
            });

            // Efectos hover en botones
            const btnPrimary = document.querySelector('.btn-primary');
            if (btnPrimary) {
                btnPrimary.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 8px 25px rgba(255, 107, 157, 0.4)';
                });
                
                btnPrimary.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            }

            const btnSecondary = document.querySelector('.btn-secondary');
            if (btnSecondary) {
                btnSecondary.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(0, 212, 255, 0.1)';
                    this.style.transform = 'translateY(-2px)';
                });
                
                btnSecondary.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
                    this.style.transform = 'translateY(0)';
                });
            }
        });

        // Función para mostrar notificaciones elegantes
        function mostrarNotificacion(mensaje, tipo = 'info') {
            const notificacion = document.createElement('div');
            notificacion.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${tipo === 'error' ? 'linear-gradient(135deg, var(--neon-pink), #ff6b9d)' : 'linear-gradient(135deg, var(--neon-green), #00ff88)'};
                color: ${tipo === 'error' ? 'white' : '#000'};
                padding: 15px 20px;
                border-radius: 10px;
                font-weight: 600;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                z-index: 10000;
                transform: translateX(100%);
                transition: all 0.3s ease;
                max-width: 300px;
            `;
            notificacion.textContent = mensaje;
            
            document.body.appendChild(notificacion);
            
            // Mostrar notificación
            setTimeout(() => {
                notificacion.style.transform = 'translateX(0)';
            }, 100);
            
            // Ocultar notificación
            setTimeout(() => {
                notificacion.style.transform = 'translateX(100%)';
                
                setTimeout(() => {
                    if (document.body.contains(notificacion)) {
                        document.body.removeChild(notificacion);
                    }
                }, 300);
            }, 4000);
        }
    </script>
</body>
</html>