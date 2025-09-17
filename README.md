# 🎓 Student System - Sistema de Gestión Académica

![Version](https://img.shields.io/badge/version-2.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Un sistema de gestión académica moderno con diseño **Cyberpunk/Retrowave**, desarrollado en PHP puro con MySQL, completamente responsive y con una interfaz de usuario elegante.

---

## 📋 Tabla de Contenidos

- [🎯 Descripción](#-descripción)
- [✨ Características](#-características)
- [🛠️ Tecnologías](#️-tecnologías)
- [📁 Estructura del Proyecto](#-estructura-del-proyecto)
- [🚀 Instalación](#-instalación)
- [📱 Uso](#-uso)
- [🎨 Diseño](#-diseño)
- [📊 Base de Datos](#-base-de-datos)
- [🔧 Configuración](#-configuración)


---

## 🎯 Descripción

**Student System** es una aplicación web completa para la gestión académica de instituciones educativas. Permite administrar estudiantes, registrar calificaciones, generar reportes estadísticos y mantener un seguimiento detallado del rendimiento académico.

### 🎮 Características Destacadas

- **Diseño Cyberpunk/Retrowave** con efectos visuales modernos
- **Completamente Responsive** - Funciona en desktop, tablet y móvil
- **Interfaz Intuitiva** con navegación fluida
- **Validaciones en Tiempo Real** para formularios
- **Reportes Estadísticos** avanzados con gráficos visuales
- **Gestión Completa** de estudiantes y calificaciones

---

## ✨ Características

### 📊 Dashboard Principal
- Estadísticas generales del sistema
- Top 3 estudiantes con medallas dinámicas
- Alertas de estudiantes en riesgo académico
- Distribución por carreras
- Acciones rápidas

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
- <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white"> - Lenguaje principal
- <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/mysql/mysql-original-wordmark.svg" alt="mysql" width="40" height="40"/> - Base de datos
- **MySQLi** - Extensión de base de datos

### Frontend
- <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white"> - Estructura semántica
- <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white"> - Estilos avanzados con Grid y Flexbox
- <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black"> - Interactividad
- **Responsive Design** - Bootstrap personalizado

### Diseño
- **Cyberpunk/Retrowave Theme** - Colores neón y gradientes
- **Custom Fonts** - Tipografías personalizadas
- **SVG Icons** - Iconografía vectorial
- **CSS Animations** - Efectos visuales suaves

### Herramientas
- **XAMPP** - Entorno de desarrollo
- <img src="https://www.vectorlogo.zone/logos/git-scm/git-scm-icon.svg" alt="git" width="40" height="40"/> - Control de versiones
- <img src="https://skillicons.dev/icons?i=vscode"/> - Editor recomendado

---

## 📁 Estructura del Proyecto

```
student-system/
├── 📄 index.php              # Dashboard principal
├── 👥 estudiantes.php        # Gestión de estudiantes
├── 📝 notas.php              # Sistema de calificaciones
├── 📊 reportes.php           # Reportes y estadísticas
├── 🗄️ datos.php              # Funciones de base de datos
├── 📖 README.md              # Documentación
├── 🎨 estilos/
│   ├── style.css             # Estilos principales
│   └── estudiantes.css       # Estilos específicos
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
   - El sistema crea automáticamente las tablas necesarias
   - Base de datos: `student_system`
   - Usuario: `root` (sin contraseña)

5. **Acceder al Sistema**
   ```
   http://localhost/student-system/
   ```

### ⚙️ Configuración Automática

El sistema incluye **configuración automática**:
- ✅ Creación de base de datos si no existe
- ✅ Creación de tablas necesarias
- ✅ Inserción de datos de prueba
- ✅ Validación de conexión

---

## 📱 Uso

### 🏠 Acceso Principal
Navega a `http://localhost/student-system/` para acceder al dashboard principal.

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

### ❌ Error de Conexión MySQL
```
Error: No se puede establecer una conexión
```
**Solución:**
1. Verificar que XAMPP esté ejecutándose
2. Iniciar MySQL en el Panel de Control
3. Verificar puerto 3306 disponible

### 🔧 Problemas de Permisos
```
Warning: mysqli_connect(): Access denied
```
**Solución:**
1. Verificar usuario y contraseña en `datos.php`
2. Resetear contraseña de MySQL en XAMPP
3. Verificar que el usuario `root` tenga permisos

### 🎨 Problemas de Estilos
**Si los estilos no cargan:**
1. Verificar ruta de archivos CSS
2. Limpiar caché del navegador
3. Verificar permisos de carpeta `estilos/`

---

### 🎯 Áreas de Mejora

- 🔐 **Autenticación y Autorización**
- 📧 **Sistema de Notificaciones**
- 📄 **Exportación PDF/Excel**
- 🎨 **Temas Personalizables**
- 📊 **Dashboard Avanzado**
- 🔍 **Búsqueda Avanzada**

---

## 👨‍💻 Autor

**Roman VSC**
- GitHub: [@romanvsc](https://github.com/romanvsc)
- Proyecto: [Student-System](https://github.com/romanvsc/Student-System)

---

## 🔗 Enlaces Útiles

- 📖 [Documentación PHP](https://www.php.net/docs.php)
- 🗄️ [MySQL Documentation](https://dev.mysql.com/doc/)
- 🎨 [CSS Grid Guide](https://css-tricks.com/snippets/css/complete-guide-grid/)
- 📱 [Responsive Design](https://web.dev/responsive-web-design-basics/)
- 🚀 [XAMPP Guide](https://www.apachefriends.org/docs/)

---

<div align="center">

### ⭐ Si te gusta este proyecto, ¡dale una estrella! ⭐

**Hecho con ❤️ y mucho ☕ por [Roman VSC](https://github.com/romanvsc)**

</div>

---

*Última actualización: Septiembre 17, 2025*
