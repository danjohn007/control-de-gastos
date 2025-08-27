<?php
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-user-plus text-primary"></i>
        Crear Usuario
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<!-- Mensajes de error -->
<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <h6><i class="fas fa-exclamation-triangle"></i> Se encontraron errores:</h6>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Formulario -->
<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user"></i> Datos del Usuario
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/usuarios/crear" id="userForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-user"></i> Nombre Completo *
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($nombre ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email *
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($email ?? '') ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Contraseña *
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       minlength="6" required>
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i> Confirmar Contraseña *
                                </label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rol" class="form-label">
                                    <i class="fas fa-user-tag"></i> Rol *
                                </label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="usuario" <?= ($rol ?? '') === 'usuario' ? 'selected' : '' ?>>
                                        Usuario Estándar
                                    </option>
                                    <option value="admin" <?= ($rol ?? '') === 'admin' ? 'selected' : '' ?>>
                                        Administrador
                                    </option>
                                </select>
                                <div class="form-text">
                                    Los administradores pueden gestionar usuarios y categorías
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">
                                    <i class="fas fa-toggle-on"></i> Estado *
                                </label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="activo" <?= ($estado ?? 'activo') === 'activo' ? 'selected' : '' ?>>
                                        Activo
                                    </option>
                                    <option value="inactivo" <?= ($estado ?? '') === 'inactivo' ? 'selected' : '' ?>>
                                        Inactivo
                                    </option>
                                </select>
                                <div class="form-text">
                                    Solo usuarios activos pueden iniciar sesión
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= BASE_URL ?>/usuarios" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Información adicional -->
    <div class="col-md-4 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i> Información
                </h6>
            </div>
            <div class="card-body">
                <h6>Roles disponibles:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <span class="badge bg-primary me-2">Administrador</span>
                        Acceso completo al sistema
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-secondary me-2">Usuario</span>
                        Acceso limitado a funciones básicas
                    </li>
                </ul>
                
                <hr>
                
                <h6>Permisos de Administrador:</h6>
                <ul class="small">
                    <li>Gestionar usuarios</li>
                    <li>Crear y editar categorías</li>
                    <li>Configurar alertas del sistema</li>
                    <li>Acceso a reportes globales</li>
                </ul>
                
                <hr>
                
                <h6>Permisos de Usuario:</h6>
                <ul class="small">
                    <li>Registrar gastos personales</li>
                    <li>Ver dashboard personal</li>
                    <li>Generar reportes propios</li>
                    <li>Configurar presupuestos</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de contraseñas en tiempo real
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePasswords() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Las contraseñas no coinciden');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
    
    // Validación del formulario
    document.getElementById('userForm').addEventListener('submit', function(e) {
        if (!App.validateForm('userForm')) {
            e.preventDefault();
            App.showNotification('Por favor, completa todos los campos requeridos', 'warning');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>