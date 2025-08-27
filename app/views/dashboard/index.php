<?php
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-tachometer-alt text-primary"></i>
        Dashboard
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= BASE_URL ?>/gastos/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Gasto
            </a>
        </div>
    </div>
</div>

<!-- Alertas de presupuesto -->
<?php if (!empty($alertas)): ?>
    <div class="alert alert-warning">
        <h6><i class="fas fa-exclamation-triangle"></i> Alertas de Presupuesto</h6>
        <ul class="mb-0">
            <?php foreach ($alertas as $alerta): ?>
                <li>
                    <strong><?= htmlspecialchars($alerta['categoria_nombre']) ?>:</strong>
                    Has gastado $<?= number_format($alerta['gastado'], 2) ?> de $<?= number_format($alerta['monto_limite'], 2) ?>
                    (<?= $alerta['porcentaje_usado'] ?>%)
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Estadísticas principales -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Gastos este mes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            $<?= number_format($totalMes, 2) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Mes anterior
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            $<?= number_format($totalMesAnterior, 2) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-<?= $porcentajeCambio >= 0 ? 'danger' : 'success' ?> shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-<?= $porcentajeCambio >= 0 ? 'danger' : 'success' ?> text-uppercase mb-1">
                            Cambio vs mes anterior
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $porcentajeCambio >= 0 ? '+' : '' ?><?= number_format($porcentajeCambio, 1) ?>%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-<?= $porcentajeCambio >= 0 ? 'arrow-up' : 'arrow-down' ?> fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total gastos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count($gastosMes) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficas -->
<div class="row">
    <!-- Gastos por categoría -->
    <div class="col-xl-6 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Gastos por Categoría - <?= date('F Y') ?></h6>
            </div>
            <div class="card-body">
                <?php if (!empty($gastosPorCategoria)): ?>
                    <div class="chart-container">
                        <canvas id="categoriasChart"></canvas>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No hay gastos registrados este mes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Gastos últimos 7 días -->
    <div class="col-xl-6 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Gastos Últimos 7 Días</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($gastosUltimos7Dias)): ?>
                    <div class="chart-container">
                        <canvas id="ultimosDiasChart"></canvas>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No hay gastos en los últimos 7 días</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Presupuestos y gastos recientes -->
<div class="row">
    <!-- Presupuestos del mes -->
    <?php if (!empty($presupuestosMes)): ?>
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-piggy-bank"></i> Presupuestos - <?= date('F Y') ?>
                </h6>
            </div>
            <div class="card-body">
                <?php foreach ($presupuestosMes as $presupuesto): ?>
                    <?php
                    $gastado = 0;
                    $porcentaje = 0;
                    foreach ($gastosPorCategoria as $gasto) {
                        if ($gasto['id'] == $presupuesto['categoria_id']) {
                            $gastado = $gasto['total'];
                            $porcentaje = ($gastado / $presupuesto['monto_limite']) * 100;
                            break;
                        }
                    }
                    $color = $porcentaje >= 90 ? 'danger' : ($porcentaje >= 70 ? 'warning' : 'success');
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span style="color: <?= $presupuesto['categoria_color'] ?>">
                                <i class="<?= $presupuesto['categoria_icono'] ?>"></i>
                                <?= htmlspecialchars($presupuesto['categoria_nombre']) ?>
                            </span>
                            <span class="font-weight-bold">
                                $<?= number_format($gastado, 2) ?> / $<?= number_format($presupuesto['monto_limite'], 2) ?>
                            </span>
                        </div>
                        <div class="progress mt-1">
                            <div class="progress-bar bg-<?= $color ?>" style="width: <?= min($porcentaje, 100) ?>%">
                                <?= number_format($porcentaje, 1) ?>%
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Gastos recientes -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clock"></i> Gastos Recientes
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($gastosMes)): ?>
                    <?php foreach (array_slice($gastosMes, 0, 5) as $gasto): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span class="text-primary" style="color: <?= $gasto['categoria_color'] ?> !important">
                                    <i class="fas fa-circle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold"><?= htmlspecialchars($gasto['categoria_nombre']) ?></div>
                                <small class="text-muted">
                                    <?= date('d/m/Y', strtotime($gasto['fecha_gasto'])) ?>
                                    <?php if ($gasto['descripcion']): ?>
                                        - <?= htmlspecialchars($gasto['descripcion']) ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="font-weight-bold">$<?= number_format($gasto['monto'], 2) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-center">
                        <a href="<?= BASE_URL ?>/gastos" class="btn btn-outline-primary btn-sm">
                            Ver todos los gastos
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No hay gastos registrados</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de gastos por categoría
    <?php if (!empty($gastosPorCategoria)): ?>
    const categoriasCtx = document.getElementById('categoriasChart').getContext('2d');
    new Chart(categoriasCtx, {
        type: 'doughnut',
        data: {
            labels: [<?php echo implode(',', array_map(function($cat) { return '"' . addslashes($cat['nombre']) . '"'; }, $gastosPorCategoria)); ?>],
            datasets: [{
                data: [<?php echo implode(',', array_column($gastosPorCategoria, 'total')); ?>],
                backgroundColor: [<?php echo implode(',', array_map(function($cat) { return '"' . $cat['color'] . '"'; }, $gastosPorCategoria)); ?>],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    <?php endif; ?>

    // Gráfico de gastos últimos 7 días
    <?php if (!empty($gastosUltimos7Dias)): ?>
    const ultimosDiasCtx = document.getElementById('ultimosDiasChart').getContext('2d');
    new Chart(ultimosDiasCtx, {
        type: 'line',
        data: {
            labels: [<?php echo implode(',', array_map(function($gasto) { return '"' . date('d/m', strtotime($gasto['fecha'])) . '"'; }, $gastosUltimos7Dias)); ?>],
            datasets: [{
                label: 'Gastos',
                data: [<?php echo implode(',', array_column($gastosUltimos7Dias, 'total')); ?>],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    <?php endif; ?>
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>