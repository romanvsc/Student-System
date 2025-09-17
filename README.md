# 🎓 Student System - Sistema de Gestión Académica

![Version](https://img.shields.io/badge/version-2.1.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![Auth](https://img.shields.io/badge/Authentication-Enabled-green.svg)

Un sistema de gestión académica moderno con diseño **Cyberpunk/Retrowave**, desarrollado en PHP puro con MySQL, completamente responsive y con una interfaz de usuario elegante. **Ahora incluye sistema de autenticación completo.**

---

## 📋 Tabla de Contenidos

- [🎯 Descripción](#-descripción)
- [✨ Características](#-características)
- [🛠️ Tecnologías](#️-tecnologías)
- [📁 Estructura del Proyecto](#-estructura-del-proyecto)
- [🚀 Instalación](#-instalación)
- [🔐 Sistema de Autenticación](#-sistema-de-autenticación)
- [📱 Uso](#-uso)
- [🎨 Diseño](#-diseño)
- [📊 Base de Datos](#-base-de-datos)
- [🔧 Configuración](#-configuración)


---

## 🎯 Descripción

**Student System** es una aplicación web completa para la gestión académica de instituciones educativas. Permite administrar estudiantes, registrar calificaciones, generar reportes estadísticos y mantener un seguimiento detallado del rendimiento académico con **sistema de autenticación seguro**.

### 🎮 Características Destacadas

- **Sistema de Login Seguro** con roles de usuario (Admin/Alumno)
- **Diseño Cyberpunk/Retrowave** con efectos visuales modernos
- **Completamente Responsive** - Funciona en desktop, tablet y móvil
- **Interfaz Intuitiva** con navegación fluida
- **Validaciones en Tiempo Real** para formularios
- **Reportes Estadísticos** avanzados con gráficos visuales
- **Gestión Completa** de estudiantes y calificaciones
- **Sesiones Seguras** con PHP Sessions

---

## ✨ Características

### 🔐 Sistema de Autenticación
- 🔑 **Login Seguro** con contraseñas hasheadas (password_hash)
- 👑 **Roles de Usuario**: Administrador y Alumno
- 🎭 **Avatares Dinámicos** con iniciales del usuario
- 🚪 **Sesiones Seguras** con configuración avanzada
- 🔄 **Redirección Inteligente** después del login
- 📱 **Diseño Responsive** del login
- ⚡ **Notificaciones Visuales** para feedback
- 🛡️ **Protección CSRF** y validación de sesiones

### 📊 Dashboard Principal
- Estadísticas generales del sistema
- Top 3 estudiantes con medallas dinámicas
- Alertas de estudiantes en riesgo académico
- Distribución por carreras
- Acciones rápidas
- **Información del usuario logueado**

### 👥 Gestión de Estudiantes
- ✅ **CRUD Completo** - Crear, Leer, Actualizar, Eliminar
- 🔍 **Búsqueda Avanzada** por nombre o email
- 📚 **Filtrado por Carrera** 
- 👁️ **Vista Detallada** con historial académico
- 📊 **Cálculo Automático** de promedios
- 📱 **Responsive Design** para móviles

### 📝 Sistema de Notas
- 📋 **Gestión de Calificaciones** con validación 0-100
- 🎯 **Vista por Estudiante** con historial completo
- 📚 **Vista por Materia** con estadísticas
- 📊 **Indicadores Visuales** de rendimiento
- 💬 **Observaciones** adicionales por nota
- ⚡ **Actualización en Tiempo Real** de promedios

### 📊 Reportes y Estadísticas
- 🏆 **Ranking Estudiantil** con posiciones
- 📈 **Estadísticas por Materia** 
- 📋 **Distribución de Calificaciones**
- 🎯 **Análisis de Rendimiento**
- 📱 **Gráficos Interactivos**
- 📄 **Exportación de Datos**

---

## 🛠️ Tecnologías

### Backend
- **PHP 8.0+** - Lenguaje principal
- **MySQL 8.0+** - Base de datos
- **MySQLi** - Extensión de base de datos
- **PHP Sessions** - Sistema de autenticación
- **password_hash()** - Hashing seguro de contraseñas

### Frontend
- **HTML5** - Estructura semántica
- **CSS3** - Estilos avanzados con Grid y Flexbox
- **JavaScript ES6+** - Interactividad
- **Responsive Design** - Bootstrap personalizado

### Seguridad
- **Contraseñas Hasheadas** - Usando bcrypt
- **Sesiones Seguras** - Configuración HTTPS-ready
- **Validación de Entrada** - Sanitización de datos
- **Protección XSS** - htmlspecialchars()

### Diseño
- **Cyberpunk/Retrowave Theme** - Colores neón y gradientes
- **Custom Fonts** - Tipografías personalizadas
- **SVG Icons** - Iconografía vectorial
- **CSS Animations** - Efectos visuales suaves
- **Login Horizontal** - Diseño profesional de 2 columnas

### Herramientas
- **XAMPP** - Entorno de desarrollo
- **Git** - Control de versiones
- **VS Code** - Editor recomendado

---

## 📁 Estructura del Proyecto

```
student-system/
├── 📄 index.php              # Dashboard principal
├── 🔐 login.php              # Página de inicio de sesión
├── 🚪 logout.php             # Cerrar sesión
├── 🛡️ auth.php               # Sistema de autenticación
├── 👥 estudiantes.php        # Gestión de estudiantes
├── 📝 notas.php              # Sistema de calificaciones
├── 📊 reportes.php           # Reportes y estadísticas
├── 🗄️ datos.php              # Funciones de base de datos
├── 📖 README.md              # Documentación
├── 🎨 estilos/
│   ├── style.css             # Estilos principales
│   ├── login.css             # Estilos específicos para login.php ✨ NUEVO
│   └── estudiantes.css       # Estilos específicos para estudiantes.php
├── 🖼️ assets/
│   ├── background.jpg        # Fondo principal
│   ├── *.svg                 # Iconos vectoriales
│   ├── medalla-*.svg         # Medallas para ranking
│   ├── titulos.ttf           # Fuente para títulos
│   └── textos.otf            # Fuente para textos
└── 🗂️ .git/                  # Control de versiones
```

---

## 🚀 Instalación

### Prerrequisitos

- **XAMPP** (PHP 8.0+, MySQL 8.0+)
- **Navegador Web Moderno**
- **Git** (opcional)

### Pasos de Instalación

1. **Clonar el Repositorio**
   ```bash
   git clone https://github.com/romanvsc/Student-System.git
   cd Student-System
   ```

2. **Configurar XAMPP**
   - Iniciar **Apache** y **MySQL** en el Panel de Control de XAMPP
   - Verificar que esté funcionando en `http://localhost`

3. **Ubicar el Proyecto**
   ```bash
   # Copiar archivos a la carpeta de XAMPP
   cp -r student-system/ C:\xampp\htdocs\
   ```

4. **Configurar Base de Datos**
   - Abrir **phpMyAdmin** (`http://localhost/phpmyadmin`)
   - Crear base de datos: `student_system`
   - Ejecutar el **SQL de usuarios** (ver sección de Autenticación)
   - El sistema crea automáticamente las demás tablas

5. **Acceder al Sistema**
   ```
   http://localhost/student-system/login.php
   ```

### ⚙️ Configuración Automática

El sistema incluye **configuración automática**:
- ✅ Creación de base de datos si no existe
- ✅ Creación de tablas necesarias
- ✅ **Tabla de usuarios con admin por defecto**
- ✅ Inserción de datos de prueba
- ✅ Validación de conexión

---

## 🔐 Sistema de Autenticación

### 🔑 Credenciales por Defecto

| Usuario | Contraseña | Tipo | Descripción |
|---------|------------|------|-------------|
| `admin` | `admin123` | 👑 Administrador | Acceso completo al sistema |

### 📋 SQL para Crear Usuarios

Ejecuta este código en **phpMyAdmin** para configurar el sistema de usuarios:

```sql
-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `tipo_usuario` enum('administrador','alumno') NOT NULL DEFAULT 'alumno',
  `estudiante_id` int(11) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `ultimo_acceso` datetime DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_estudiante_id` (`estudiante_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Agregar clave foránea
ALTER TABLE `usuarios` 
ADD CONSTRAINT `fk_usuarios_estudiantes` 
FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes`(`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Insertar usuario administrador
INSERT INTO `usuarios` VALUES 
(1,'admin','admin@studentsystem.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Administrador del Sistema','administrador',NULL,NULL,1,NULL,NOW(),NOW());
```

### 🎯 Tipos de Usuario

#### 👑 **Administrador**
- ✅ **Acceso completo** al sistema
- ✅ **Gestión de usuarios**
- ✅ **Todas las funcionalidades**
- ✅ **Reportes avanzados**
- ✅ **Configuración del sistema**

#### 👨‍🎓 **Alumno**
- ✅ **Vista de sus calificaciones**
- ✅ **Información personal**
- ✅ **Reportes limitados**
- ❌ No puede modificar otros estudiantes
- ❌ No puede gestionar usuarios

### 🛡️ Características de Seguridad

- **Contraseñas Hasheadas**: Usando `password_hash()` con bcrypt
- **Sesiones Seguras**: Configuración para HTTPS y protección XSS
- **Validación de Entrada**: Sanitización con `htmlspecialchars()`
- **Protección CSRF**: Tokens de sesión seguros
- **Logout Automático**: Control de sesiones expiradas

---

## 📱 Uso

### 🔐 Inicio de Sesión
1. **Ir a**: `http://localhost/student-system/login.php`
2. **Credenciales por defecto**: `admin` / `admin123`
3. **El sistema te redirige** al dashboard según tu rol
4. **Avatar automático**: Muestra iniciales del nombre

### 🏠 Dashboard (Autenticado)
- **Header superior** muestra información del usuario
- **Menú desplegable** con opciones de perfil
- **Estado de sesión** visible
- **Botón de logout** siempre disponible

### 👥 Gestión de Estudiantes
1. **Crear Estudiante**: Clic en "➕ Nuevo Estudiante"
2. **Buscar**: Utilizar el campo de búsqueda por nombre o email
3. **Filtrar**: Seleccionar carrera específica
4. **Ver Detalle**: Clic en "👁️ Ver" en cualquier estudiante
5. **Editar**: Clic en "✏️ Editar" para modificar datos
6. **Eliminar**: Clic en "🗑️ Eliminar" con confirmación

### 📝 Registro de Notas
1. **Nueva Nota**: Clic en "➕ Registrar Nueva Nota"
2. **Seleccionar Estudiante**: Dropdown con búsqueda
3. **Seleccionar Materia**: Según la carrera del estudiante
4. **Ingresar Calificación**: Validación 0-100 con indicadores visuales
5. **Agregar Observaciones**: Comentarios opcionales
6. **Guardar**: Validación automática y confirmación

### 📊 Reportes
1. **Ranking Estudiantil**: Top estudiantes con medallas
2. **Estadísticas por Materia**: Análisis detallado
3. **Distribución de Calificaciones**: Visualización por rangos
4. **Filtros Dinámicos**: Por carrera y rango de fechas

---

## 🎨 Diseño

### 🌈 Paleta de Colores Cyberpunk
```css
--midnight-void: #0a0a1a        /* Fondo principal */
--neon-pink: #ff2c7a            /* Acentos principales */
--cyber-blue: #00d4ff           /* Enlaces y títulos */
--neon-green: #39ff14           /* Éxito y aprobados */
--sunset-orange: #f5a905        /* Advertencias */
--pure-white: #ffffff           /* Textos */
--hologram-purple: #b0b0ff      /* Elementos secundarios */
```

### ✨ Efectos Visuales
- **Gradientes Neón** en tarjetas y botones
- **Backdrop Blur** para transparencias
- **Animaciones CSS** suaves y fluidas
- **Hover Effects** interactivos
- **Box Shadows** con colores neón
- **Text Shadows** para efectos de brillo
- **Partículas Flotantes** en el login
- **Efectos de Brillo** en avatares

### 🔐 Diseño del Login
- **Layout Horizontal** de 2 columnas profesional
- **Columna Izquierda**: Branding y características del sistema
- **Columna Derecha**: Formulario de login compacto
- **Animaciones Cyberpunk**: Partículas y efectos neón
- **Completamente Responsive**: Se adapta a móviles

### 📱 Responsive Breakpoints
```css
/* Mobile First Design */
@media (max-width: 480px)   /* Móviles pequeños */
@media (max-width: 768px)   /* Móviles y tablets pequeñas */
@media (max-width: 1024px)  /* Tablets */
@media (min-width: 1200px)  /* Desktop grande */
```

---

## 📊 Base de Datos

### 📋 Esquema de Tablas

#### 🔐 `usuarios` ✨ NUEVA
```sql
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('administrador','alumno') NOT NULL DEFAULT 'alumno',
    estudiante_id INT NULL,
    avatar VARCHAR(255) NULL,
    activo BOOLEAN DEFAULT TRUE,
    ultimo_acceso DATETIME NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE SET NULL
);
```

#### 🏫 `carreras`
```sql
CREATE TABLE carreras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL UNIQUE,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 👨‍🎓 `estudiantes`
```sql
CREATE TABLE estudiantes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    carrera_id INT NOT NULL,
    semestre INT NOT NULL,
    fecha_ingreso DATE NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (carrera_id) REFERENCES carreras(id) ON DELETE CASCADE
);
```

#### 📚 `materias`
```sql
CREATE TABLE materias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    carrera_id INT NOT NULL,
    creditos INT DEFAULT 3,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (carrera_id) REFERENCES carreras(id) ON DELETE CASCADE
);
```

#### 📝 `notas`
```sql
CREATE TABLE notas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    estudiante_id INT NOT NULL,
    materia_id INT NOT NULL,
    nota DECIMAL(5,2) NOT NULL,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY estudiante_materia_unique (estudiante_id, materia_id),
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id) ON DELETE CASCADE,
    FOREIGN KEY (materia_id) REFERENCES materias(id) ON DELETE CASCADE
);
```

### 🔗 Relaciones
- **Usuarios** → **Estudiantes** (1:1 opcional)
- **Estudiantes** → **Carreras** (N:1)
- **Materias** → **Carreras** (N:1)
- **Notas** → **Estudiantes** (N:1)
- **Notas** → **Materias** (N:1)

---

## 🔧 Configuración

### ⚙️ Configuración de Base de Datos
```php
// En datos.php
class Database {
    private $host = 'localhost';      // Servidor MySQL
    private $db_name = 'student_system'; // Nombre de BD
    private $username = 'root';       // Usuario MySQL
    private $password = '';           // Contraseña (vacía por defecto)
}
```

### 🔐 Configuración de Autenticación
```php
// En auth.php
function inicializarSesionSegura() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS
        session_start();
    }
}
```

### 🎨 Personalización de Colores
```css
/* En estilos/style.css */
:root {
    --midnight-void: #0a0a1a;     /* Cambiar fondo principal */
    --neon-pink: #ff2c7a;         /* Cambiar color de acentos */
    --cyber-blue: #00d4ff;        /* Cambiar color de enlaces */
    /* ... más variables ... */
}
```

### ❌ Solución de Problemas

#### **Error de Conexión MySQL**
```
Error: No se puede establecer una conexión
```
**Solución:**
1. Verificar que XAMPP esté ejecutándose
2. Iniciar MySQL en el Panel de Control
3. Verificar puerto 3306 disponible

#### **Problemas de Autenticación**
```
Warning: session_start(): Session already started
```
**Solución:**
1. Verificar que no hay múltiples llamadas a `session_start()`
2. Usar la función `inicializarSesionSegura()` de [`auth.php`](auth.php )
3. Limpiar caché del navegador

#### **Error de Permisos**
```
Warning: mysqli_connect(): Access denied
```
**Solución:**
1. Verificar usuario y contraseña en [`datos.php`](datos.php )
2. Resetear contraseña de MySQL en XAMPP
3. Verificar que el usuario `root` tenga permisos

#### **Problemas de Estilos**
**Si los estilos no cargan:**
1. Verificar ruta de archivos CSS (especialmente [`login.css`](estilos/login.css ))
2. Limpiar caché del navegador
3. Verificar permisos de carpeta [`estilos/`](estilos/)

---

### 🎯 Funcionalidades Futuras

- 🔐 **Recuperación de Contraseña** vía email
- 👥 **Gestión Avanzada de Usuarios** (por administradores)
- 🖼️ **Subida de Avatares** personalizados
- 📧 **Sistema de Notificaciones** por email
- 📄 **Exportación PDF/Excel** mejorada
- 🎨 **Temas Personalizables** por usuario
- 📊 **Dashboard Avanzado** según rol
- 🔍 **Búsqueda Avanzada** con filtros
- 📱 **App Móvil** complementaria

---

## 👨‍💻 Autor

**Roman VSC**
- GitHub: [@romanvsc](https://github.com/romanvsc)
- Proyecto: [Student-System](https://github.com/romanvsc/Student-System)

---

## 🔗 Enlaces Útiles

- 📖 [Documentación PHP](https://www.php.net/docs.php)
- 🗄️ [MySQL Documentation](https://dev.mysql.com/doc/)
- 🔐 [PHP Sessions](https://www.php.net/manual/en/book.session.php)
- 🛡️ [PHP Security](https://www.php.net/manual/en/security.php)
- 🎨 [CSS Grid Guide](https://css-tricks.com/snippets/css/complete-guide-grid/)
- 📱 [Responsive Design](https://web.dev/responsive-web-design-basics/)
- 🚀 [XAMPP Guide](https://www.apachefriends.org/docs/)

---

<div align="center">

### ⭐ Si te gusta este proyecto, ¡dale una estrella! ⭐

**Hecho con ❤️ y mucho ☕ por [Roman VSC](https://github.com/romanvsc)**

</div>

---

*Última actualización: Septiembre 10, 2025 - Sistema de Autenticación v2.1.0*