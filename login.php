<?php
require_once 'auth.php';

// Si ya está autenticado, redireccionar según el tipo de usuario
if (estaAutenticado()) {
    if (esAlumno()) {
        header('Location: estudiante_panel.php');
    } else {
        $redirect = $_GET['redirect'] ?? 'index.php';
        header('Location: ' . $redirect);
    }
    exit;
}

$error = '';
$mensaje = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        if (procesarLogin($username, $password)) {
            // Login exitoso - redirigir según el tipo de usuario
            if (esAlumno()) {
                header('Location: estudiante_panel.php');
            } else {
                $redirect = $_POST['redirect'] ?? 'index.php';
                header('Location: ' . $redirect);
            }
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}

// Obtener mensaje de error desde URL
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'no_auth':
            $error = 'Debes iniciar sesión para acceder a esta página.';
            break;
        case 'sin_permisos':
            $error = 'No tienes permisos para acceder a esta sección.';
            break;
        case 'sesion_expirada':
            $error = 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.';
            break;
    }
}

if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'logout':
            $mensaje = 'Sesión cerrada exitosamente.';
            break;
        case 'registro_exitoso':
            $mensaje = 'Usuario registrado exitosamente. Ya puedes iniciar sesión.';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Student System</title>
    <link rel="stylesheet" href="estilos/style.css">
    <link rel="stylesheet" href="estilos/login.css">
</head>

<body>
    <!-- Partículas de fondo -->
    <div class="particles" id="particles"></div>

    <div class="login-container">
        <!-- Columna Izquierda - Branding -->
        <div class="login-left">
            <div class="brand-section">
                <h1 class="brand-logo">🎓 STUDENT SYSTEM</h1>
            </div>
        </div>

        <!-- Columna Derecha - Formulario -->
        <div class="login-right">
            <!-- Header del formulario -->
            <div class="login-header">
                <h1 class="login-logo">Iniciar Sesión</h1>
                <p class="login-subtitle">Accede a tu panel de control</p>
                <p class="login-description">Ingresa tus credenciales para continuar</p>
            </div>

            <!-- Alertas -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    🚨 <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-success">
                    ✅ <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de Login -->
            <form class="login-form" method="POST" action="login.php">
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? 'index.php'); ?>">
                
                <div class="form-group">
                    <label class="form-label" for="username">👤 Usuario o Email</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input" 
                        placeholder="Ingresa tu usuario o email"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">🔒 Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Ingresa tu contraseña"
                        required
                    >
                </div>
                
                <button type="submit" class="login-button">
                    🚀 Iniciar Sesión
                </button>
            </form>

            <!-- Credenciales de demostración -->
            <!-- 
            <div class="demo-credentials">
                <h4>🔑 Credenciales de Demostración</h4>
                <div class="credentials-grid">
                    <div class="credential-item">
                        <strong>👨‍💼 Administrador:</strong>
                        <span>admin / admin123</span>
                    </div>
                    <div class="credential-item">
                        <strong>👨‍🎓 Estudiantes:</strong>
                        <span>Consulta con admin</span>
                    </div>
                </div>
            </div>
            -->

            <!-- Footer -->
            <div class="login-footer">
                <p>💡 Desarrollado con tecnología <strong>Cyberpunk</strong></p>
                <p>🌐 <strong>Roman VSC</strong> - Student Management System</p>
            </div>
        </div>
    </div>

    <script>
        // Crear partículas animadas
        function crearParticulas() {
            const particlesContainer = document.getElementById('particles');
            const numeroParticulas = 50;
            
            for (let i = 0; i < numeroParticulas; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Posición inicial aleatoria
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                
                // Retraso de animación aleatorio
                particle.style.animationDelay = Math.random() * 6 + 's';
                
                // Color aleatorio entre azul y rosa
                const colores = ['#00d4ff', '#ff2c7a', '#b0b0ff'];
                particle.style.background = colores[Math.floor(Math.random() * colores.length)];
                
                particlesContainer.appendChild(particle);
            }
        }

        // Efecto de typing en el placeholder
        function effectoTyping() {
            const inputs = document.querySelectorAll('.form-input');
            
            inputs.forEach(input => {
                const originalPlaceholder = input.placeholder;
                
                input.addEventListener('focus', function() {
                    this.placeholder = '';
                    let i = 0;
                    const typing = setInterval(() => {
                        if (i < originalPlaceholder.length) {
                            this.placeholder += originalPlaceholder.charAt(i);
                            i++;
                        } else {
                            clearInterval(typing);
                        }
                    }, 50);
                });
                
                input.addEventListener('blur', function() {
                    if (this.value === '') {
                        this.placeholder = originalPlaceholder;
                    }
                });
            });
        }

        // Inicializar efectos
        document.addEventListener('DOMContentLoaded', function() {
            crearParticulas();
            effectoTyping();
            
            // Autofocus en el primer input
            document.getElementById('username').focus();
            
            // Efecto de shake en error
            if (document.querySelector('.alert-error')) {
                const rightPanel = document.querySelector('.login-right');
                if (rightPanel) {
                    rightPanel.style.animation = 'shake 0.5s ease-in-out';
                    
                    setTimeout(() => {
                        rightPanel.style.animation = '';
                    }, 500);
                }
            }
        });
    </script>
</body>
</html>
