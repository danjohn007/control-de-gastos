<?php
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-receipt text-primary"></i>
        Mis Gastos
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= BASE_URL ?>/gastos/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Gasto
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

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="card-title mb-0">
            <i class="fas fa-filter"></i> Filtros
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/gastos" class="row g-3">
            <div class="col-md-3">
                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                       value="<?= htmlspecialchars($filtros['fecha_inicio']) ?>">
            </div>
            <div class="col-md-3">
                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                       value="<?= htmlspecialchars($filtros['fecha_fin']) ?>">
            </div>
            <div class="col-md-3">
                <label for="categoria_id" class="form-label">Categoría</label>
                <select class="form-select" id="categoria_id" name="categoria_id">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['id'] ?>" 
                                <?= $filtros['categoria_id'] == $categoria['id'] ? 'selected' : '' ?>>
                            <?php if ($categoria['categoria_padre_id']): ?>
                                &nbsp;&nbsp;&nbsp;└ 
                            <?php endif; ?>
                            <?= htmlspecialchars($categoria['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="metodo_pago_id" class="form-label">Método de Pago</label>
                <select class="form-select" id="metodo_pago_id" name="metodo_pago_id">
                    <option value="">Todos los métodos</option>
                    <?php foreach ($metodosPago as $metodo): ?>
                        <option value="<?= $metodo['id'] ?>" 
                                <?= $filtros['metodo_pago_id'] == $metodo['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($metodo['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="busqueda" class="form-label">Búsqueda</label>
                <input type="text" class="form-control" id="busqueda" name="busqueda" 
                       value="<?= htmlspecialchars($filtros['busqueda']) ?>"
                       placeholder="Buscar en descripción...">
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="<?= BASE_URL ?>/gastos" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar"></i> Períodos
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="setDateFilter('today')">Hoy</a></li>
                            <li><a class="dropdown-item" href="#" onclick="setDateFilter('week')">Esta semana</a></li>
                            <li><a class="dropdown-item" href="#" onclick="setDateFilter('month')">Este mes</a></li>
                            <li><a class="dropdown-item" href="#" onclick="setDateFilter('year')">Este año</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Resumen -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Gastos</h6>
                        <h4 class="mb-0">$<?= number_format($total, 2) ?></h4>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Cantidad</h6>
                        <h4 class="mb-0"><?= count($gastos) ?></h4>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Promedio</h6>
                        <h4 class="mb-0">$<?= count($gastos) > 0 ? number_format($total / count($gastos), 2) : '0.00' ?></h4>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de gastos -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list"></i> Lista de Gastos
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($gastos)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Método</th>
                            <th>Monto</th>
                            <th>Comprobante</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gastos as $gasto): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($gasto['fecha_gasto'])) ?></td>
                                <td>
                                    <span style="color: <?= $gasto['categoria_color'] ?>">
                                        <i class="<?= $gasto['categoria_icono'] ?>"></i>
                                    </span>
                                    <?= htmlspecialchars($gasto['categoria_nombre']) ?>
                                </td>
                                <td>
                                    <?= $gasto['descripcion'] ? htmlspecialchars($gasto['descripcion']) : '<em class="text-muted">Sin descripción</em>' ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($gasto['metodo_pago_nombre']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <strong>$<?= number_format($gasto['monto'], 2) ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php if ($gasto['comprobante']): ?>
                                        <a href="<?= BASE_URL ?>/uploads/<?= $gasto['comprobante'] ?>" 
                                           target="_blank" class="btn btn-outline-info btn-sm" title="Ver comprobante">
                                            <i class="fas fa-file"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>/gastos/<?= $gasto['id'] ?>/editar" 
                                           class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal<?= $gasto['id'] ?>"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal de confirmación de eliminación -->
                            <div class="modal fade" id="deleteModal<?= $gasto['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Estás seguro de que deseas eliminar este gasto?</p>
                                            <div class="alert alert-info">
                                                <strong>Fecha:</strong> <?= date('d/m/Y', strtotime($gasto['fecha_gasto'])) ?><br>
                                                <strong>Categoría:</strong> <?= htmlspecialchars($gasto['categoria_nombre']) ?><br>
                                                <strong>Monto:</strong> $<?= number_format($gasto['monto'], 2) ?><br>
                                                <?php if ($gasto['descripcion']): ?>
                                                    <strong>Descripción:</strong> <?= htmlspecialchars($gasto['descripcion']) ?>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Esta acción no se puede deshacer.
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Cancelar
                                            </button>
                                            <form method="POST" action="<?= BASE_URL ?>/gastos/<?= $gasto['id'] ?>/eliminar" 
                                                  style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
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
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay gastos registrados</h5>
                <p class="text-muted">
                    <?php if (array_filter($filtros)): ?>
                        No se encontraron gastos con los filtros aplicados
                    <?php else: ?>
                        Registra tu primer gasto
                    <?php endif; ?>
                </p>
                <a href="<?= BASE_URL ?>/gastos/crear" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Registrar Gasto
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funciones para filtros rápidos de fecha se cargan desde app.js
    
    // Auto-submit del formulario cuando cambian los filtros
    const filtros = document.querySelectorAll('#categoria_id, #metodo_pago_id');
    filtros.forEach(filtro => {
        filtro.addEventListener('change', function() {
            // Opcional: auto-submit cuando cambia filtro
            // this.form.submit();
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>