<?php
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tags text-primary"></i>
        Gestión de Categorías
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= BASE_URL ?>/categorias/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Categoría
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

<!-- Tabla de categorías -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list"></i> Lista de Categorías
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($categorias)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2" style="color: <?= $categoria['color'] ?>">
                                            <i class="<?= $categoria['icono'] ?>"></i>
                                        </span>
                                        <div>
                                            <strong><?= htmlspecialchars($categoria['nombre']) ?></strong>
                                            <?php if ($categoria['categoria_padre_id']): ?>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-arrow-right"></i>
                                                    Subcategoría
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        <?= $categoria['descripcion'] ? htmlspecialchars($categoria['descripcion']) : 'Sin descripción' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($categoria['categoria_padre_id']): ?>
                                        <span class="badge bg-info">
                                            <i class="fas fa-arrow-right"></i> Subcategoría
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-folder"></i> Principal
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($categoria['estado'] === 'activo'): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($categoria['fecha_creacion'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>/categorias/<?= $categoria['id'] ?>/editar" 
                                           class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-outline-danger btn-delete" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal<?= $categoria['id'] ?>"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal de confirmación de eliminación -->
                            <div class="modal fade" id="deleteModal<?= $categoria['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Estás seguro de que deseas eliminar la categoría 
                                               <strong style="color: <?= $categoria['color'] ?>">
                                                   <i class="<?= $categoria['icono'] ?>"></i>
                                                   <?= htmlspecialchars($categoria['nombre']) ?>
                                               </strong>?</p>
                                            <p class="text-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Esta acción no se puede deshacer. Solo se pueden eliminar categorías sin subcategorías o gastos asociados.
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Cancelar
                                            </button>
                                            <form method="POST" action="<?= BASE_URL ?>/categorias/<?= $categoria['id'] ?>/eliminar" 
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
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay categorías registradas</h5>
                <p class="text-muted">Crea la primera categoría del sistema</p>
                <a href="<?= BASE_URL ?>/categorias/crear" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Categoría
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Estadísticas -->
<?php if (!empty($categorias)): ?>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">
                        <?= count($categorias) ?>
                    </h5>
                    <p class="card-text">Total Categorías</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">
                        <?= count(array_filter($categorias, function($c) { return $c['estado'] === 'activo'; })) ?>
                    </h5>
                    <p class="card-text">Categorías Activas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-info">
                        <?= count(array_filter($categorias, function($c) { return $c['categoria_padre_id'] === null; })) ?>
                    </h5>
                    <p class="card-text">Categorías Principales</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">
                        <?= count(array_filter($categorias, function($c) { return $c['categoria_padre_id'] !== null; })) ?>
                    </h5>
                    <p class="card-text">Subcategorías</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Vista previa de categorías por color -->
<?php if (!empty($categorias)): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="fas fa-palette"></i> Vista Previa de Colores
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach (array_filter($categorias, function($c) { return $c['estado'] === 'activo'; }) as $categoria): ?>
                    <div class="col-md-3 col-sm-4 col-6 mb-3">
                        <div class="d-flex align-items-center p-2 border rounded" 
                             style="background: linear-gradient(45deg, <?= $categoria['color'] ?>22, transparent);">
                            <span class="me-2" style="color: <?= $categoria['color'] ?>; font-size: 1.5em;">
                                <i class="<?= $categoria['icono'] ?>"></i>
                            </span>
                            <div>
                                <small class="fw-bold"><?= htmlspecialchars($categoria['nombre']) ?></small>
                                <br>
                                <small class="text-muted"><?= $categoria['color'] ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>