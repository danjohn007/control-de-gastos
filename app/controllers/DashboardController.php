<?php
require_once __DIR__ . '/Controller.php';

/**
 * Controlador del Dashboard
 */
class DashboardController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $gastoModel = $this->loadModel('Gasto');
        $categoriaModel = $this->loadModel('Categoria');
        $presupuestoModel = $this->loadModel('Presupuesto');
        
        $userId = $_SESSION['user_id'];
        $currentMonth = date('Y-m');
        $currentYear = date('Y');
        
        // Estadísticas del mes actual
        $gastosMes = $gastoModel->getGastosDelMes($userId, $currentMonth);
        $totalMes = array_sum(array_column($gastosMes, 'monto'));
        
        // Gastos por categoría del mes
        $gastosPorCategoria = $gastoModel->getGastosPorCategoria($userId, $currentMonth);
        
        // Gastos de los últimos 7 días
        $gastosUltimos7Dias = $gastoModel->getGastosUltimosDias($userId, 7);
        
        // Presupuestos del mes
        $presupuestosMes = $presupuestoModel->getPresupuestosDelMes($userId, date('n'), $currentYear);
        
        // Alertas de presupuesto
        $alertas = $presupuestoModel->getAlertasPresupuesto($userId);
        
        // Categorías más utilizadas
        $categoriasMasUsadas = $gastoModel->getCategoriasMasUsadas($userId, 5);
        
        // Comparación con el mes anterior
        $mesAnterior = date('Y-m', strtotime('-1 month'));
        $gastosMesAnterior = $gastoModel->getGastosDelMes($userId, $mesAnterior);
        $totalMesAnterior = array_sum(array_column($gastosMesAnterior, 'monto'));
        
        $porcentajeCambio = 0;
        if ($totalMesAnterior > 0) {
            $porcentajeCambio = (($totalMes - $totalMesAnterior) / $totalMesAnterior) * 100;
        }
        
        $this->loadView('dashboard/index', [
            'title' => 'Dashboard',
            'totalMes' => $totalMes,
            'totalMesAnterior' => $totalMesAnterior,
            'porcentajeCambio' => $porcentajeCambio,
            'gastosPorCategoria' => $gastosPorCategoria,
            'gastosUltimos7Dias' => $gastosUltimos7Dias,
            'presupuestosMes' => $presupuestosMes,
            'alertas' => $alertas,
            'categoriasMasUsadas' => $categoriasMasUsadas,
            'gastosMes' => $gastosMes
        ]);
    }
}