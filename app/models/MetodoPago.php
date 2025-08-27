<?php
/**
 * Modelo de Método de Pago
 */
class MetodoPago extends Model {
    protected $table = 'metodos_pago';
    
    public function getMetodosActivos() {
        return $this->findAll(['estado' => 'activo'], 'nombre');
    }
    
    public function getTodosLosMetodos() {
        return $this->findAll([], 'nombre');
    }
}