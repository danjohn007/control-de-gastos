<?php
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-users text-primary"></i>
        Gestión de Usuarios
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= BASE_URL ?>/usuarios/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </a>
        </div>
    </div>
</div>

<!-- Mensajes -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Tabla de usuarios -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list"></i> Lista de Usuarios
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($usuarios)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= $usuario['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($usuario['nombre']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td>
                                    <?php if ($usuario['rol'] === 'admin'): ?>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-crown"></i> Administrador
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-user"></i> Usuario
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($usuario['estado'] === 'activo'): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>/usuarios/<?= $usuario['id'] ?>/editar" 
                                           class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                            <button type="button" class="btn btn-outline-danger btn-delete" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal<?= $usuario['id'] ?>"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal de confirmación de eliminación -->
                            <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                <div class="modal fade" id="deleteModal<?= $usuario['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Estás seguro de que deseas eliminar el usuario 
                                                   <strong><?= htmlspecialchars($usuario['nombre']) ?></strong>?</p>
                                                <p class="text-warning">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Esta acción no se puede deshacer.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Cancelar
                                                </button>
                                                <form method="POST" action="<?= BASE_URL ?>/usuarios/<?= $usuario['id'] ?>/eliminar" 
                                                      style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay usuarios registrados</h5>
                <p class="text-muted">Crea el primer usuario del sistema</p>
                <a href="<?= BASE_URL ?>/usuarios/crear" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Usuario
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Estadísticas -->
<?php if (!empty($usuarios)): ?>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">
                        <?= count($usuarios) ?>
                    </h5>
                    <p class="card-text">Total Usuarios</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">
                        <?= count(array_filter($usuarios, function($u) { return $u['estado'] === 'activo'; })) ?>
                    </h5>
                    <p class="card-text">Usuarios Activos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">
                        <?= count(array_filter($usuarios, function($u) { return $u['rol'] === 'admin'; })) ?>
                    </h5>
                    <p class="card-text">Administradores</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-info">
                        <?= count(array_filter($usuarios, function($u) { return $u['rol'] === 'usuario'; })) ?>
                    </h5>
                    <p class="card-text">Usuarios Estándar</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>