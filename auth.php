<?php
// Función para inicializar sesión de forma segura
function inicializarSesionSegura() {
    if (session_status() === PHP_SESSION_NONE) {
        // Configurar parámetros de sesión seguros
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS
        
        session_start();
    }
}

// Inicializar sesión
inicializarSesionSegura();
require_once 'datos.php';

/**
 * Inicializar sesión de usuario
 */
function iniciarSesion($usuario) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['username'] = $usuario['username'];
    $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
    $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
    $_SESSION['estudiante_id'] = $usuario['estudiante_id'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['ultimo_acceso'] = $usuario['ultimo_acceso'];
    $_SESSION['autenticado'] = true;
}

/**
 * Cerrar sesión
 */
function cerrarSesion() {
    session_unset();
    session_destroy();
}

/**
 * Verificar si el usuario está autenticado
 */
function estaAutenticado() {
    return isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true;
}

/**
 * Verificar si el usuario es administrador
 */
function esAdministrador() {
    return estaAutenticado() && $_SESSION['tipo_usuario'] === 'administrador';
}

/**
 * Verificar si el usuario es alumno
 */
function esAlumno() {
    return estaAutenticado() && $_SESSION['tipo_usuario'] === 'alumno';
}

/**
 * Obtener datos del usuario actual
 */
function obtenerUsuarioActual() {
    if (!estaAutenticado()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['usuario_id'],
        'username' => $_SESSION['username'],
        'nombre_completo' => $_SESSION['nombre_completo'],
        'tipo_usuario' => $_SESSION['tipo_usuario'],
        'estudiante_id' => $_SESSION['estudiante_id'],
        'email' => $_SESSION['email'],
        'ultimo_acceso' => $_SESSION['ultimo_acceso']
    ];
}

/**
 * Obtener iniciales del usuario para avatar
 */
function obtenerInicialesUsuario() {
    if (!estaAutenticado()) {
        return 'GU'; // Guest User
    }
    
    $nombre = $_SESSION['nombre_completo'];
    $partes = explode(' ', $nombre);
    
    if (count($partes) >= 2) {
        return strtoupper(substr($partes[0], 0, 1) . substr($partes[1], 0, 1));
    } else {
        return strtoupper(substr($nombre, 0, 2));
    }
}

/**
 * Redireccionar si no está autenticado
 */
function requiereAutenticacion() {
    if (!estaAutenticado()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/**
 * Redireccionar si no es administrador
 */
function requiereAdministrador() {
    requiereAutenticacion();
    
    if (!esAdministrador()) {
        header('Location: index.php?error=sin_permisos');
        exit;
    }
}

/**
 * Procesar login
 */
function procesarLogin($username, $password) {
    $usuario = autenticarUsuario($username, $password);
    
    if ($usuario) {
        iniciarSesion($usuario);
        return true;
    }
    
    return false;
}
?>
