# 💰 Sistema de Control de Gastos

Un sistema completo para el registro, organización y análisis de gastos personales, familiares o empresariales con categorización personalizada.

## 🚀 Características Principales

- **Gestión de Usuarios**: Sistema de roles (administrador/usuario)
- **Categorización**: Organización de gastos en categorías y subcategorías
- **Registro de Gastos**: Captura detallada con comprobantes
- **Dashboard Interactivo**: Gráficas y estadísticas en tiempo real
- **Presupuestos**: Control y alertas de límites por categoría
- **Reportes**: Exportación a PDF, Excel y CSV
- **Búsqueda Avanzada**: Filtros combinados por fecha, categoría, etc.
- **Diseño Responsivo**: Compatible con dispositivos móviles

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7+ (puro, sin framework)
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript ES6+
- **Framework CSS**: Bootstrap 5
- **Gráficas**: Chart.js
- **Calendario**: FullCalendar.js
- **Iconos**: Font Awesome 6
- **Arquitectura**: MVC (Modelo-Vista-Controlador)

## 📋 Requisitos del Sistema

- Apache 2.4+ con mod_rewrite habilitado
- PHP 7.4+ con extensiones:
  - PDO MySQL
  - GD (para manipulación de imágenes)
  - FileInfo (para validación de archivos)
- MySQL 5.7+ o MariaDB 10.2+
- Al menos 100MB de espacio en disco

## 🔧 Instalación

### 1. Descargar el Sistema

```bash
git clone https://github.com/danjohn007/control-de-gastos.git
cd control-de-gastos
```

### 2. Configurar el Servidor Web

#### Apache (recomendado)

1. Copiar los archivos al directorio del servidor web:
   ```bash
   # Para instalación en el directorio raíz
   sudo cp -R * /var/www/html/
   
   # Para instalación en subdirectorio
   sudo cp -R * /var/www/html/gastos/
   ```

2. Configurar permisos:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/gastos/
   sudo chmod -R 755 /var/www/html/gastos/
   sudo chmod -R 777 /var/www/html/gastos/uploads/
   ```

3. Habilitar mod_rewrite (si no está habilitado):
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

### 3. Configurar la Base de Datos

1. Crear la base de datos:
   ```sql
   CREATE DATABASE control_gastos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Importar el esquema:
   ```bash
   mysql -u root -p control_gastos < database/schema.sql
   ```

### 4. Configurar la Aplicación

1. Editar el archivo `config/config.php`:
   ```php
   // Configuración de la base de datos
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'control_gastos');
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_contraseña');
   ```

2. Verificar que el directorio `uploads/` tenga permisos de escritura:
   ```bash
   chmod 777 uploads/
   ```

### 5. Configuración del Virtual Host (Opcional)

Para una configuración más profesional, crear un virtual host:

```apache
<VirtualHost *:80>
    ServerName gastos.local
    DocumentRoot /var/www/html/gastos
    
    <Directory /var/www/html/gastos>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/gastos_error.log
    CustomLog ${APACHE_LOG_DIR}/gastos_access.log combined
</VirtualHost>
```

## 🎯 Acceso al Sistema

### URL de Acceso

- **Instalación en raíz**: `http://tu-dominio.com/`
- **Instalación en subdirectorio**: `http://tu-dominio.com/gastos/`
- **Local**: `http://localhost/gastos/`

### Credenciales Predeterminadas

#### Administrador
- **Email**: admin@gastos.com
- **Contraseña**: password

#### Usuario de Prueba
- **Email**: juan@email.com
- **Contraseña**: password

> ⚠️ **Importante**: Cambiar las contraseñas predeterminadas después de la instalación.

## 📁 Estructura del Proyecto

```
control-de-gastos/
├── app/
│   ├── controllers/          # Controladores MVC
│   ├── models/              # Modelos de datos
│   └── views/               # Vistas/Templates
├── config/
│   └── config.php           # Configuración principal
├── database/
│   └── schema.sql           # Esquema de base de datos
├── public/
│   ├── css/                 # Hojas de estilo
│   ├── js/                  # Scripts JavaScript
│   └── img/                 # Imágenes
├── uploads/                 # Archivos subidos
├── .htaccess               # Configuración Apache
├── index.php               # Punto de entrada
└── README.md               # Esta documentación
```

## 🔧 Configuración Avanzada

### Variables de Entorno

Editar `config/config.php` para personalizar:

```php
// Tiempo de sesión (en segundos)
define('SESSION_TIMEOUT', 3600);

// Tamaño máximo de archivo (en bytes)
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Extensiones permitidas para comprobantes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

// Zona horaria
date_default_timezone_set('America/Mexico_City');
```

### SSL/HTTPS

Para habilitar HTTPS, modificar `.htaccess`:

```apache
# Redirigir a HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## 🎨 Personalización

### Cambiar Colores del Tema

Editar `public/css/style.css`:

```css
:root {
    --primary-color: #007bff;      /* Color principal */
    --secondary-color: #6c757d;    /* Color secundario */
    --success-color: #28a745;      /* Color de éxito */
    --danger-color: #dc3545;       /* Color de peligro */
}
```

### Agregar Nuevas Categorías

1. Acceder como administrador
2. Ir a "Categorías" → "Crear Categoría"
3. Configurar nombre, color e icono Font Awesome

### Personalizar Dashboard

Modificar `app/views/dashboard/index.php` para ajustar:
- Métricas mostradas
- Tipos de gráficas
- Períodos de análisis

## 🔒 Seguridad

### Medidas Implementadas

- Autenticación con hash de contraseñas (password_hash)
- Protección CSRF en formularios
- Validación de archivos subidos
- Control de sesiones con timeout
- Sanitización de datos de entrada
- Roles y permisos diferenciados

### Recomendaciones Adicionales

1. **Cambiar credenciales predeterminadas**
2. **Usar HTTPS en producción**
3. **Mantener PHP y MySQL actualizados**
4. **Realizar backups regulares**
5. **Configurar firewall del servidor**

## 📊 Funcionalidades por Rol

### Administrador
- ✅ Gestión completa de usuarios
- ✅ Crear/editar/eliminar categorías
- ✅ Configurar presupuestos y alertas
- ✅ Acceso a todos los reportes
- ✅ Administración del sistema

### Usuario Estándar
- ✅ Registrar y gestionar gastos personales
- ✅ Ver dashboard personal
- ✅ Generar reportes propios
- ✅ Configurar presupuestos personales
- ❌ No puede gestionar otros usuarios
- ❌ No puede gestionar categorías del sistema

## 🔧 Solución de Problemas

### Error de Conexión a Base de Datos
```
Error de conexión a la base de datos: SQLSTATE[HY000] [1045]
```
**Solución**: Verificar credenciales en `config/config.php`

### Páginas en Blanco
**Posibles causas**:
- Errores de PHP (revisar logs del servidor)
- mod_rewrite no habilitado
- Permisos de archivos incorrectos

### Archivos no se Suben
**Verificar**:
- Permisos del directorio `uploads/` (777)
- Configuración `upload_max_filesize` en PHP
- Configuración `post_max_size` en PHP

### URLs No Funcionan
**Verificar**:
- mod_rewrite habilitado
- Archivo `.htaccess` presente
- `AllowOverride All` en configuración Apache

## 🔄 Actualización

1. **Hacer backup** de la base de datos y archivos
2. **Descargar** la nueva versión
3. **Reemplazar** archivos (mantener `config/config.php` y `uploads/`)
4. **Ejecutar** scripts de migración si los hay
5. **Verificar** funcionamiento

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama para nueva característica (`git checkout -b feature/nueva-caracteristica`)
3. Commit los cambios (`git commit -am 'Agregar nueva característica'`)
4. Push a la rama (`git push origin feature/nueva-caracteristica`)
5. Crear Pull Request

## 📞 Soporte

Para soporte técnico o reportar bugs:
- **Email**: soporte@gastos.com
- **GitHub Issues**: [Crear Issue](https://github.com/danjohn007/control-de-gastos/issues)

## 📈 Roadmap

- [ ] API REST para aplicaciones móviles
- [ ] Notificaciones push
- [ ] Integración con bancos
- [ ] Análisis predictivo con IA
- [ ] Aplicación móvil nativa
- [ ] Multi-idioma
- [ ] Temas personalizables

---

**Desarrollado con ❤️ para una mejor gestión financiera personal y empresarial.**
