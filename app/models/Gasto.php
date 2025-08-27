<?php
/**
 * Modelo de Gasto
 */
class Gasto extends Model {
    protected $table = 'gastos';
    
    public function getGastosConDetalles($userId, $filtros = []) {
        $sql = "SELECT g.*, c.nombre as categoria_nombre, c.color as categoria_color, 
                       c.icono as categoria_icono, mp.nombre as metodo_pago_nombre,
                       u.nombre as usuario_nombre
                FROM {$this->table} g 
                INNER JOIN categorias c ON g.categoria_id = c.id 
                INNER JOIN metodos_pago mp ON g.metodo_pago_id = mp.id 
                INNER JOIN usuarios u ON g.usuario_id = u.id
                WHERE g.usuario_id = ?";
        
        $params = [$userId];
        
        // Aplicar filtros
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND g.fecha_gasto >= ?";
            $params[] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND g.fecha_gasto <= ?";
            $params[] = $filtros['fecha_fin'];
        }
        
        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND g.categoria_id = ?";
            $params[] = $filtros['categoria_id'];
        }
        
        if (!empty($filtros['metodo_pago_id'])) {
            $sql .= " AND g.metodo_pago_id = ?";
            $params[] = $filtros['metodo_pago_id'];
        }
        
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (g.descripcion LIKE ? OR c.nombre LIKE ?)";
            $busqueda = '%' . $filtros['busqueda'] . '%';
            $params[] = $busqueda;
            $params[] = $busqueda;
        }
        
        $sql .= " ORDER BY g.fecha_gasto DESC, g.fecha_creacion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getGastosDelMes($userId, $mes) {
        $sql = "SELECT g.*, c.nombre as categoria_nombre, c.color as categoria_color 
                FROM {$this->table} g 
                INNER JOIN categorias c ON g.categoria_id = c.id 
                WHERE g.usuario_id = ? AND DATE_FORMAT(g.fecha_gasto, '%Y-%m') = ?
                ORDER BY g.fecha_gasto DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $mes]);
        return $stmt->fetchAll();
    }
    
    public function getGastosPorCategoria($userId, $mes) {
        $sql = "SELECT c.id, c.nombre, c.color, c.icono, SUM(g.monto) as total
                FROM {$this->table} g 
                INNER JOIN categorias c ON g.categoria_id = c.id 
                WHERE g.usuario_id = ? AND DATE_FORMAT(g.fecha_gasto, '%Y-%m') = ?
                GROUP BY c.id, c.nombre, c.color, c.icono
                ORDER BY total DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $mes]);
        return $stmt->fetchAll();
    }
    
    public function getGastosUltimosDias($userId, $dias) {
        $sql = "SELECT DATE(g.fecha_gasto) as fecha, SUM(g.monto) as total
                FROM {$this->table} g 
                WHERE g.usuario_id = ? AND g.fecha_gasto >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(g.fecha_gasto)
                ORDER BY fecha ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $dias]);
        return $stmt->fetchAll();
    }
    
    public function getCategoriasMasUsadas($userId, $limite = 5) {
        $sql = "SELECT c.nombre, c.color, c.icono, COUNT(g.id) as cantidad, SUM(g.monto) as total
                FROM {$this->table} g 
                INNER JOIN categorias c ON g.categoria_id = c.id 
                WHERE g.usuario_id = ? AND YEAR(g.fecha_gasto) = YEAR(CURDATE())
                GROUP BY c.id, c.nombre, c.color, c.icono
                ORDER BY cantidad DESC, total DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limite]);
        return $stmt->fetchAll();
    }
    
    public function getGastosPorPeriodo($userId, $fechaInicio, $fechaFin) {
        $sql = "SELECT g.*, c.nombre as categoria_nombre, c.color as categoria_color,
                       mp.nombre as metodo_pago_nombre
                FROM {$this->table} g 
                INNER JOIN categorias c ON g.categoria_id = c.id 
                INNER JOIN metodos_pago mp ON g.metodo_pago_id = mp.id
                WHERE g.usuario_id = ? AND g.fecha_gasto BETWEEN ? AND ?
                ORDER BY g.fecha_gasto DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $fechaInicio, $fechaFin]);
        return $stmt->fetchAll();
    }
    
    public function getTotalGastosUsuario($userId) {
        $sql = "SELECT SUM(monto) FROM {$this->table} WHERE usuario_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 0;
    }
    
    public function eliminarGasto($id, $userId) {
        // Verificar que el gasto pertenece al usuario
        $gasto = $this->find($id);
        if (!$gasto || $gasto['usuario_id'] != $userId) {
            return false;
        }
        
        // Eliminar archivo adjunto si existe
        if (!empty($gasto['comprobante']) && file_exists(UPLOAD_PATH . $gasto['comprobante'])) {
            unlink(UPLOAD_PATH . $gasto['comprobante']);
        }
        
        return $this->delete($id);
    }
}