<?php
ob_start();
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2><i class="fas fa-user-plus text-primary"></i> Registro</h2>
            <p class="text-muted">Crea tu cuenta</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>/register">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="mb-3">
                <label for="nombre" class="form-label">
                    <i class="fas fa-user"></i> Nombre Completo
                </label>
                <input type="text" class="form-control" id="nombre" name="nombre" 
                       value="<?= htmlspecialchars($nombre ?? '') ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Contraseña
                </label>
                <input type="password" class="form-control" id="password" name="password" 
                       minlength="6" required>
                <div class="form-text">La contraseña debe tener al menos 6 caracteres</div>
            </div>
            
            <div class="mb-3">
                <label for="confirm_password" class="form-label">
                    <i class="fas fa-lock"></i> Confirmar Contraseña
                </label>
                <input type="password" class="form-control" id="confirm_password" 
                       name="confirm_password" required>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>
            </div>
        </form>
        
        <div class="text-center mt-3">
            <p class="text-muted">
                ¿Ya tienes cuenta? 
                <a href="<?= BASE_URL ?>/login" class="text-decoration-none">Inicia sesión aquí</a>
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>