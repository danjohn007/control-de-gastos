<?php
/**
 * Modelo de Categoría
 */
class Categoria extends Model {
    protected $table = 'categorias';
    
    public function getCategorias($incluirInactivas = false) {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!$incluirInactivas) {
            $sql .= " WHERE estado = 'activo'";
        }
        
        $sql .= " ORDER BY categoria_padre_id IS NULL DESC, categoria_padre_id, nombre";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getCategoriasPrincipales() {
        return $this->findAll(['categoria_padre_id' => null, 'estado' => 'activo'], 'nombre');
    }
    
    public function getSubcategorias($categoriaPadreId) {
        return $this->findAll(['categoria_padre_id' => $categoriaPadreId, 'estado' => 'activo'], 'nombre');
    }
    
    public function getCategoriasConSubcategorias() {
        $categorias = $this->getCategoriasPrincipales();
        
        foreach ($categorias as &$categoria) {
            $categoria['subcategorias'] = $this->getSubcategorias($categoria['id']);
        }
        
        return $categorias;
    }
    
    public function tieneDependencias($id) {
        // Verificar si tiene subcategorías
        $subcategorias = $this->count(['categoria_padre_id' => $id]);
        if ($subcategorias > 0) {
            return true;
        }
        
        // Verificar si tiene gastos asociados
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM gastos WHERE categoria_id = ?");
        $stmt->execute([$id]);
        $gastos = $stmt->fetchColumn();
        
        return $gastos > 0;
    }
    
    public function eliminarCategoria($id) {
        if ($this->tieneDependencias($id)) {
            return false;
        }
        
        return $this->delete($id);
    }
    
    public function obtenerRutaCompleta($id) {
        $categoria = $this->find($id);
        if (!$categoria) {
            return '';
        }
        
        $ruta = [$categoria['nombre']];
        
        if ($categoria['categoria_padre_id']) {
            $padre = $this->find($categoria['categoria_padre_id']);
            if ($padre) {
                array_unshift($ruta, $padre['nombre']);
            }
        }
        
        return implode(' > ', $ruta);
    }
}