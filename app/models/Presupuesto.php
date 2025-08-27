<?php
/**
 * Modelo de Presupuesto
 */
class Presupuesto extends Model {
    protected $table = 'presupuestos';
    
    public function getPresupuestosDelMes($userId, $mes, $año) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre, c.color as categoria_color, c.icono as categoria_icono
                FROM {$this->table} p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.usuario_id = ? AND p.mes = ? AND p.año = ? AND p.estado = 'activo'
                ORDER BY c.nombre";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $mes, $año]);
        return $stmt->fetchAll();
    }
    
    public function getPresupuestosConGastos($userId, $mes, $año) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre, c.color as categoria_color, 
                       c.icono as categoria_icono,
                       COALESCE(SUM(g.monto), 0) as gastado,
                       (p.monto_limite - COALESCE(SUM(g.monto), 0)) as restante,
                       ROUND((COALESCE(SUM(g.monto), 0) / p.monto_limite) * 100, 2) as porcentaje_usado
                FROM {$this->table} p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                LEFT JOIN gastos g ON p.categoria_id = g.categoria_id 
                    AND g.usuario_id = p.usuario_id 
                    AND MONTH(g.fecha_gasto) = p.mes 
                    AND YEAR(g.fecha_gasto) = p.año
                WHERE p.usuario_id = ? AND p.mes = ? AND p.año = ? AND p.estado = 'activo'
                GROUP BY p.id, c.nombre, c.color, c.icono
                ORDER BY porcentaje_usado DESC, c.nombre";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $mes, $año]);
        return $stmt->fetchAll();
    }
    
    public function getAlertasPresupuesto($userId) {
        $mesActual = date('n');
        $añoActual = date('Y');
        
        $sql = "SELECT p.*, c.nombre as categoria_nombre, c.color as categoria_color,
                       COALESCE(SUM(g.monto), 0) as gastado,
                       ROUND((COALESCE(SUM(g.monto), 0) / p.monto_limite) * 100, 2) as porcentaje_usado
                FROM {$this->table} p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                LEFT JOIN gastos g ON p.categoria_id = g.categoria_id 
                    AND g.usuario_id = p.usuario_id 
                    AND MONTH(g.fecha_gasto) = p.mes 
                    AND YEAR(g.fecha_gasto) = p.año
                WHERE p.usuario_id = ? AND p.mes = ? AND p.año = ? AND p.estado = 'activo'
                GROUP BY p.id, c.nombre, c.color
                HAVING porcentaje_usado >= 80
                ORDER BY porcentaje_usado DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $mesActual, $añoActual]);
        return $stmt->fetchAll();
    }
    
    public function existePresupuesto($userId, $categoriaId, $mes, $año, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE usuario_id = ? AND categoria_id = ? AND mes = ? AND año = ?";
        $params = [$userId, $categoriaId, $mes, $año];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
    
    public function getPresupuestosUsuario($userId, $incluirInactivos = false) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre, c.color as categoria_color
                FROM {$this->table} p 
                INNER JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.usuario_id = ?";
        
        if (!$incluirInactivos) {
            $sql .= " AND p.estado = 'activo'";
        }
        
        $sql .= " ORDER BY p.año DESC, p.mes DESC, c.nombre";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function verificarYCrearAlertas($userId) {
        $alertas = $this->getAlertasPresupuesto($userId);
        
        foreach ($alertas as $presupuesto) {
            // Verificar si ya existe una alerta para este presupuesto en el periodo actual
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM alertas_presupuesto 
                 WHERE usuario_id = ? AND presupuesto_id = ? 
                 AND MONTH(fecha_alerta) = ? AND YEAR(fecha_alerta) = ?"
            );
            $stmt->execute([
                $userId, 
                $presupuesto['id'], 
                date('n'), 
                date('Y')
            ]);
            
            if ($stmt->fetchColumn() == 0) {
                // Crear nueva alerta
                $mensaje = "Has usado el {$presupuesto['porcentaje_usado']}% del presupuesto de {$presupuesto['categoria_nombre']}";
                
                $stmt = $this->db->prepare(
                    "INSERT INTO alertas_presupuesto (usuario_id, presupuesto_id, porcentaje_alerta, mensaje) 
                     VALUES (?, ?, ?, ?)"
                );
                $stmt->execute([
                    $userId,
                    $presupuesto['id'],
                    round($presupuesto['porcentaje_usado']),
                    $mensaje
                ]);
            }
        }
    }
}