<?php
ob_start();
?>

<div class="container-fluid">
    <div class="text-center">
        <h1 class="display-1 text-muted">404</h1>
        <h3>Página no encontrada</h3>
        <p class="text-muted">La página que buscas no existe o ha sido movida.</p>
        
        <div class="mt-4">
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary">
                <i class="fas fa-home"></i> Ir al Dashboard
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>