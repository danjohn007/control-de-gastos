<?php
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-user-edit text-primary"></i>
        Editar Usuario
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
                <form method="POST" action="<?= BASE_URL ?>/usuarios/<?= $usuario['id'] ?>/editar" id="userForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-user"></i> Nombre Completo *
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email *
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Nueva Contraseña
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       minlength="6">
                                <div class="form-text">Dejar en blanco para mantener la actual</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i> Confirmar Contraseña
                                </label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password">
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
                                    <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>
                                        Usuario Estándar
                                    </option>
                                    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>
                                        Administrador
                                    </option>
                                </select>
                                <?php if ($usuario['id'] == $_SESSION['user_id']): ?>
                                    <div class="form-text text-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No puedes cambiar tu propio rol
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">
                                    <i class="fas fa-toggle-on"></i> Estado *
                                </label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="activo" <?= $usuario['estado'] === 'activo' ? 'selected' : '' ?>>
                                        Activo
                                    </option>
                                    <option value="inactivo" <?= $usuario['estado'] === 'inactivo' ? 'selected' : '' ?>>
                                        Inactivo
                                    </option>
                                </select>
                                <?php if ($usuario['id'] == $_SESSION['user_id']): ?>
                                    <div class="form-text text-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No puedes desactivar tu propia cuenta
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= BASE_URL ?>/usuarios" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Información del usuario -->
    <div class="col-md-4 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i> Información del Usuario
                </h6>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">ID:</dt>
                    <dd class="col-sm-8"><?= $usuario['id'] ?></dd>
                    
                    <dt class="col-sm-4">Creado:</dt>
                    <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])) ?></dd>
                    
                    <dt class="col-sm-4">Actualizado:</dt>
                    <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($usuario['fecha_actualizacion'])) ?></dd>
                </dl>
                
                <?php if ($usuario['id'] == $_SESSION['user_id']): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Este es tu usuario actual. Algunas opciones están limitadas por seguridad.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Estadísticas del usuario -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-bar"></i> Estadísticas
                </h6>
            </div>
            <div class="card-body">
                <?php
                // Aquí podrías cargar estadísticas del usuario
                // Por ahora solo mostramos placeholders
                ?>
                <div class="text-center">
                    <div class="row">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary">0</h4>
                                <small class="text-muted">Gastos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">$0.00</h4>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentUserId = <?= $_SESSION['user_id'] ?>;
    const editingUserId = <?= $usuario['id'] ?>;
    
    // Si es el usuario actual, deshabilitar ciertas opciones
    if (currentUserId === editingUserId) {
        document.getElementById('rol').disabled = true;
        document.getElementById('estado').disabled = true;
    }
    
    // Validación de contraseñas en tiempo real
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePasswords() {
        if (password.value && password.value !== confirmPassword.value) {
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