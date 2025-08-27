<?php
ob_start();
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2><i class="fas fa-chart-line text-primary"></i> <?= APP_NAME ?></h2>
            <p class="text-muted">Inicia sesión para continuar</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['expired'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-clock"></i> Tu sesión ha expirado. Por favor, inicia sesión nuevamente.
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>/login">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
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
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </div>
        </form>
        
        <div class="text-center mt-3">
            <p class="text-muted">
                ¿No tienes cuenta? 
                <a href="<?= BASE_URL ?>/register" class="text-decoration-none">Regístrate aquí</a>
            </p>
        </div>
        
        <div class="mt-4 text-center">
            <small class="text-muted">
                <strong>Credenciales de prueba:</strong><br>
                Admin: admin@gastos.com / password<br>
                Usuario: juan@email.com / password
            </small>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>