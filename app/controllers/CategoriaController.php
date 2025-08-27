<?php
require_once __DIR__ . '/Controller.php';

/**
 * Controlador de Categorías (solo para administradores)
 */
class CategoriaController extends Controller {
    
    public function index() {
        $this->requireAdmin();
        
        $categoriaModel = $this->loadModel('Categoria');
        $categorias = $categoriaModel->getCategorias(true); // incluir inactivas
        
        $this->loadView('categorias/index', [
            'title' => 'Gestión de Categorías',
            'categorias' => $categorias,
            'csrf_token' => $this->csrf()
        ]);
    }
    
    public function create() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
        } else {
            $categoriaModel = $this->loadModel('Categoria');
            $categoriasPrincipales = $categoriaModel->getCategoriasPrincipales();
            
            $this->loadView('categorias/create', [
                'title' => 'Crear Categoría',
                'csrf_token' => $this->csrf(),
                'categoriasPrincipales' => $categoriasPrincipales
            ]);
        }
    }
    
    public function store() {
        $this->requireAdmin();
        
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $color = $_POST['color'] ?? '#007bff';
        $icono = $_POST['icono'] ?? 'fas fa-tag';
        $categoriaPadreId = !empty($_POST['categoria_padre_id']) ? $_POST['categoria_padre_id'] : null;
        $estado = $_POST['estado'] ?? 'activo';
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        $errors = [];
        
        // Validar CSRF
        if (!$this->validateCsrf($csrf_token)) {
            $errors[] = 'Token de seguridad inválido';
        }
        
        // Validaciones
        if (empty($nombre)) $errors[] = 'El nombre es requerido';
        if (strlen($nombre) > 100) $errors[] = 'El nombre no puede tener más de 100 caracteres';
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) $errors[] = 'Color inválido';
        if (!in_array($estado, ['activo', 'inactivo'])) $errors[] = 'Estado inválido';
        
        // Validar que la categoría padre existe si se especifica
        if ($categoriaPadreId) {
            $categoriaModel = $this->loadModel('Categoria');
            $categoriaPadre = $categoriaModel->find($categoriaPadreId);
            if (!$categoriaPadre) {
                $errors[] = 'La categoría padre no existe';
            } elseif ($categoriaPadre['categoria_padre_id']) {
                $errors[] = 'No se pueden crear subcategorías de subcategorías';
            }
        }
        
        if (!empty($errors)) {
            $categoriaModel = $this->loadModel('Categoria');
            $categoriasPrincipales = $categoriaModel->getCategoriasPrincipales();
            
            $this->loadView('categorias/create', [
                'errors' => $errors,
                'csrf_token' => $this->csrf(),
                'title' => 'Crear Categoría',
                'categoriasPrincipales' => $categoriasPrincipales,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'color' => $color,
                'icono' => $icono,
                'categoria_padre_id' => $categoriaPadreId,
                'estado' => $estado
            ]);
            return;
        }
        
        // Crear categoría
        $categoriaModel = $this->loadModel('Categoria');
        $categoriaId = $categoriaModel->create([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'color' => $color,
            'icono' => $icono,
            'categoria_padre_id' => $categoriaPadreId,
            'estado' => $estado
        ]);
        
        if ($categoriaId) {
            $this->redirect('/categorias?success=Categoría creada exitosamente');
        } else {
            $this->loadView('categorias/create', [
                'error' => 'Error al crear categoría',
                'csrf_token' => $this->csrf(),
                'title' => 'Crear Categoría',
                'categoriasPrincipales' => $categoriasPrincipales,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'color' => $color,
                'icono' => $icono,
                'categoria_padre_id' => $categoriaPadreId,
                'estado' => $estado
            ]);
        }
    }
    
    public function edit($id) {
        $this->requireAdmin();
        
        $categoriaModel = $this->loadModel('Categoria');
        $categoria = $categoriaModel->find($id);
        
        if (!$categoria) {
            $this->redirect('/categorias?error=Categoría no encontrada');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
        } else {
            $categoriasPrincipales = $categoriaModel->getCategoriasPrincipales();
            
            $this->loadView('categorias/edit', [
                'title' => 'Editar Categoría',
                'categoria' => $categoria,
                'csrf_token' => $this->csrf(),
                'categoriasPrincipales' => $categoriasPrincipales
            ]);
        }
    }
    
    public function update($id) {
        $this->requireAdmin();
        
        $categoriaModel = $this->loadModel('Categoria');
        $categoria = $categoriaModel->find($id);
        
        if (!$categoria) {
            $this->redirect('/categorias?error=Categoría no encontrada');
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $color = $_POST['color'] ?? '#007bff';
        $icono = $_POST['icono'] ?? 'fas fa-tag';
        $categoriaPadreId = !empty($_POST['categoria_padre_id']) ? $_POST['categoria_padre_id'] : null;
        $estado = $_POST['estado'] ?? 'activo';
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        $errors = [];
        
        // Validar CSRF
        if (!$this->validateCsrf($csrf_token)) {
            $errors[] = 'Token de seguridad inválido';
        }
        
        // Validaciones
        if (empty($nombre)) $errors[] = 'El nombre es requerido';
        if (strlen($nombre) > 100) $errors[] = 'El nombre no puede tener más de 100 caracteres';
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) $errors[] = 'Color inválido';
        if (!in_array($estado, ['activo', 'inactivo'])) $errors[] = 'Estado inválido';
        
        // Validar que la categoría padre existe si se especifica
        if ($categoriaPadreId) {
            $categoriaPadre = $categoriaModel->find($categoriaPadreId);
            if (!$categoriaPadre) {
                $errors[] = 'La categoría padre no existe';
            } elseif ($categoriaPadre['categoria_padre_id']) {
                $errors[] = 'No se pueden crear subcategorías de subcategorías';
            } elseif ($categoriaPadreId == $id) {
                $errors[] = 'Una categoría no puede ser padre de sí misma';
            }
        }
        
        if (!empty($errors)) {
            $categoriasPrincipales = $categoriaModel->getCategoriasPrincipales();
            
            $this->loadView('categorias/edit', [
                'errors' => $errors,
                'csrf_token' => $this->csrf(),
                'title' => 'Editar Categoría',
                'categoriasPrincipales' => $categoriasPrincipales,
                'categoria' => array_merge($categoria, [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'color' => $color,
                    'icono' => $icono,
                    'categoria_padre_id' => $categoriaPadreId,
                    'estado' => $estado
                ])
            ]);
            return;
        }
        
        // Actualizar categoría
        $success = $categoriaModel->update($id, [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'color' => $color,
            'icono' => $icono,
            'categoria_padre_id' => $categoriaPadreId,
            'estado' => $estado
        ]);
        
        if ($success) {
            $this->redirect('/categorias?success=Categoría actualizada exitosamente');
        } else {
            $this->loadView('categorias/edit', [
                'error' => 'Error al actualizar categoría',
                'csrf_token' => $this->csrf(),
                'title' => 'Editar Categoría',
                'categoriasPrincipales' => $categoriasPrincipales,
                'categoria' => array_merge($categoria, [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'color' => $color,
                    'icono' => $icono,
                    'categoria_padre_id' => $categoriaPadreId,
                    'estado' => $estado
                ])
            ]);
        }
    }
    
    public function delete($id) {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/categorias?error=Método no permitido');
        }
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrf($csrf_token)) {
            $this->redirect('/categorias?error=Token de seguridad inválido');
        }
        
        $categoriaModel = $this->loadModel('Categoria');
        $categoria = $categoriaModel->find($id);
        
        if (!$categoria) {
            $this->redirect('/categorias?error=Categoría no encontrada');
        }
        
        // Verificar dependencias
        if ($categoriaModel->tieneDependencias($id)) {
            $this->redirect('/categorias?error=No se puede eliminar una categoría con subcategorías o gastos asociados');
        }
        
        $success = $categoriaModel->delete($id);
        
        if ($success) {
            $this->redirect('/categorias?success=Categoría eliminada exitosamente');
        } else {
            $this->redirect('/categorias?error=Error al eliminar categoría');
        }
    }
}