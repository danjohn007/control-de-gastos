<?php
require_once __DIR__ . '/Controller.php';

/**
 * Controlador de Gastos
 */
class GastoController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $gastoModel = $this->loadModel('Gasto');
        $userId = $_SESSION['user_id'];
        
        // Obtener filtros
        $filtros = [
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? '',
            'categoria_id' => $_GET['categoria_id'] ?? '',
            'metodo_pago_id' => $_GET['metodo_pago_id'] ?? '',
            'busqueda' => $_GET['busqueda'] ?? ''
        ];
        
        // Si no hay filtros de fecha, mostrar del mes actual
        if (empty($filtros['fecha_inicio']) && empty($filtros['fecha_fin'])) {
            $filtros['fecha_inicio'] = date('Y-m-01');
            $filtros['fecha_fin'] = date('Y-m-t');
        }
        
        $gastos = $gastoModel->getGastosConDetalles($userId, $filtros);
        
        // Cargar datos para filtros
        $categoriaModel = $this->loadModel('Categoria');
        $metodoModel = $this->loadModel('MetodoPago');
        $categorias = $categoriaModel->getCategorias();
        $metodosPago = $metodoModel->getMetodosActivos();
        
        // Calcular total
        $total = array_sum(array_column($gastos, 'monto'));
        
        $this->loadView('gastos/index', [
            'title' => 'Mis Gastos',
            'gastos' => $gastos,
            'categorias' => $categorias,
            'metodosPago' => $metodosPago,
            'filtros' => $filtros,
            'total' => $total,
            'csrf_token' => $this->csrf()
        ]);
    }
    
    public function create() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
        } else {
            // Cargar datos para el formulario
            $categoriaModel = $this->loadModel('Categoria');
            $metodoModel = $this->loadModel('MetodoPago');
            $categorias = $categoriaModel->getCategorias();
            $metodosPago = $metodoModel->getMetodosActivos();
            
            $this->loadView('gastos/create', [
                'title' => 'Registrar Gasto',
                'categorias' => $categorias,
                'metodosPago' => $metodosPago,
                'csrf_token' => $this->csrf()
            ]);
        }
    }
    
    public function store() {
        $this->requireAuth();
        
        $monto = (float)($_POST['monto'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');
        $fecha_gasto = $_POST['fecha_gasto'] ?? '';
        $categoria_id = (int)($_POST['categoria_id'] ?? 0);
        $metodo_pago_id = (int)($_POST['metodo_pago_id'] ?? 0);
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        $errors = [];
        
        // Validar CSRF
        if (!$this->validateCsrf($csrf_token)) {
            $errors[] = 'Token de seguridad inválido';
        }
        
        // Validaciones
        if ($monto <= 0) $errors[] = 'El monto debe ser mayor a 0';
        if ($monto > 999999.99) $errors[] = 'El monto no puede ser mayor a $999,999.99';
        if (empty($fecha_gasto)) $errors[] = 'La fecha es requerida';
        if ($categoria_id <= 0) $errors[] = 'Selecciona una categoría válida';
        if ($metodo_pago_id <= 0) $errors[] = 'Selecciona un método de pago válido';
        
        // Validar fecha
        if (!empty($fecha_gasto)) {
            $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha_gasto);
            if (!$fechaValida || $fechaValida->format('Y-m-d') !== $fecha_gasto) {
                $errors[] = 'Fecha inválida';
            }
        }
        
        // Validar que la categoría y método de pago existen y están activos
        if (empty($errors)) {
            $categoriaModel = $this->loadModel('Categoria');
            $metodoModel = $this->loadModel('MetodoPago');
            
            $categoria = $categoriaModel->find($categoria_id);
            if (!$categoria || $categoria['estado'] !== 'activo') {
                $errors[] = 'Categoría no válida';
            }
            
            $metodoPago = $metodoModel->find($metodo_pago_id);
            if (!$metodoPago || $metodoPago['estado'] !== 'activo') {
                $errors[] = 'Método de pago no válido';
            }
        }
        
        // Manejar archivo adjunto
        $nombreArchivo = null;
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['comprobante'];
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            
            // Validar archivo
            if (!in_array($extension, ALLOWED_EXTENSIONS)) {
                $errors[] = 'Tipo de archivo no permitido. Solo se permiten: ' . implode(', ', ALLOWED_EXTENSIONS);
            } elseif ($archivo['size'] > MAX_FILE_SIZE) {
                $errors[] = 'El archivo es demasiado grande. Tamaño máximo: ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB';
            } else {
                // Generar nombre único
                $nombreArchivo = 'comprobante_' . uniqid() . '.' . $extension;
                $rutaDestino = UPLOAD_PATH . 'comprobantes/' . $nombreArchivo;
                
                if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                    $errors[] = 'Error al subir el archivo';
                    $nombreArchivo = null;
                }
            }
        }
        
        if (!empty($errors)) {
            // Cargar datos para mostrar errores
            $categoriaModel = $this->loadModel('Categoria');
            $metodoModel = $this->loadModel('MetodoPago');
            $categorias = $categoriaModel->getCategorias();
            $metodosPago = $metodoModel->getMetodosActivos();
            
            $this->loadView('gastos/create', [
                'errors' => $errors,
                'csrf_token' => $this->csrf(),
                'title' => 'Registrar Gasto',
                'categorias' => $categorias,
                'metodosPago' => $metodosPago,
                'monto' => $monto,
                'descripcion' => $descripcion,
                'fecha_gasto' => $fecha_gasto,
                'categoria_id' => $categoria_id,
                'metodo_pago_id' => $metodo_pago_id
            ]);
            return;
        }
        
        // Crear gasto
        $gastoModel = $this->loadModel('Gasto');
        $gastoId = $gastoModel->create([
            'usuario_id' => $_SESSION['user_id'],
            'categoria_id' => $categoria_id,
            'metodo_pago_id' => $metodo_pago_id,
            'monto' => $monto,
            'descripcion' => $descripcion,
            'fecha_gasto' => $fecha_gasto,
            'comprobante' => $nombreArchivo ? 'comprobantes/' . $nombreArchivo : null
        ]);
        
        if ($gastoId) {
            $this->redirect('/gastos?success=Gasto registrado exitosamente');
        } else {
            // Eliminar archivo si se subió pero falló la inserción
            if ($nombreArchivo && file_exists(UPLOAD_PATH . 'comprobantes/' . $nombreArchivo)) {
                unlink(UPLOAD_PATH . 'comprobantes/' . $nombreArchivo);
            }
            
            $categoriaModel = $this->loadModel('Categoria');
            $metodoModel = $this->loadModel('MetodoPago');
            $categorias = $categoriaModel->getCategorias();
            $metodosPago = $metodoModel->getMetodosActivos();
            
            $this->loadView('gastos/create', [
                'error' => 'Error al registrar gasto',
                'csrf_token' => $this->csrf(),
                'title' => 'Registrar Gasto',
                'categorias' => $categorias,
                'metodosPago' => $metodosPago,
                'monto' => $monto,
                'descripcion' => $descripcion,
                'fecha_gasto' => $fecha_gasto,
                'categoria_id' => $categoria_id,
                'metodo_pago_id' => $metodo_pago_id
            ]);
        }
    }
    
    public function edit($id) {
        $this->requireAuth();
        
        $gastoModel = $this->loadModel('Gasto');
        $gasto = $gastoModel->find($id);
        
        if (!$gasto || $gasto['usuario_id'] != $_SESSION['user_id']) {
            $this->redirect('/gastos?error=Gasto no encontrado');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
        } else {
            // Cargar datos para el formulario
            $categoriaModel = $this->loadModel('Categoria');
            $metodoModel = $this->loadModel('MetodoPago');
            $categorias = $categoriaModel->getCategorias();
            $metodosPago = $metodoModel->getMetodosActivos();
            
            $this->loadView('gastos/edit', [
                'title' => 'Editar Gasto',
                'gasto' => $gasto,
                'categorias' => $categorias,
                'metodosPago' => $metodosPago,
                'csrf_token' => $this->csrf()
            ]);
        }
    }
    
    public function update($id) {
        $this->requireAuth();
        
        $gastoModel = $this->loadModel('Gasto');
        $gasto = $gastoModel->find($id);
        
        if (!$gasto || $gasto['usuario_id'] != $_SESSION['user_id']) {
            $this->redirect('/gastos?error=Gasto no encontrado');
        }
        
        $monto = (float)($_POST['monto'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');
        $fecha_gasto = $_POST['fecha_gasto'] ?? '';
        $categoria_id = (int)($_POST['categoria_id'] ?? 0);
        $metodo_pago_id = (int)($_POST['metodo_pago_id'] ?? 0);
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        $errors = [];
        
        // Validar CSRF
        if (!$this->validateCsrf($csrf_token)) {
            $errors[] = 'Token de seguridad inválido';
        }
        
        // Validaciones (mismo código que en store)
        if ($monto <= 0) $errors[] = 'El monto debe ser mayor a 0';
        if ($monto > 999999.99) $errors[] = 'El monto no puede ser mayor a $999,999.99';
        if (empty($fecha_gasto)) $errors[] = 'La fecha es requerida';
        if ($categoria_id <= 0) $errors[] = 'Selecciona una categoría válida';
        if ($metodo_pago_id <= 0) $errors[] = 'Selecciona un método de pago válido';
        
        // Validar fecha
        if (!empty($fecha_gasto)) {
            $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha_gasto);
            if (!$fechaValida || $fechaValida->format('Y-m-d') !== $fecha_gasto) {
                $errors[] = 'Fecha inválida';
            }
        }
        
        // Validar que la categoría y método de pago existen
        if (empty($errors)) {
            $categoriaModel = $this->loadModel('Categoria');
            $metodoModel = $this->loadModel('MetodoPago');
            
            $categoria = $categoriaModel->find($categoria_id);
            if (!$categoria || $categoria['estado'] !== 'activo') {
                $errors[] = 'Categoría no válida';
            }
            
            $metodoPago = $metodoModel->find($metodo_pago_id);
            if (!$metodoPago || $metodoPago['estado'] !== 'activo') {
                $errors[] = 'Método de pago no válido';
            }
        }
        
        // Manejar nuevo archivo adjunto
        $nombreArchivo = $gasto['comprobante']; // Mantener el actual por defecto
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['comprobante'];
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            
            // Validar archivo
            if (!in_array($extension, ALLOWED_EXTENSIONS)) {
                $errors[] = 'Tipo de archivo no permitido';
            } elseif ($archivo['size'] > MAX_FILE_SIZE) {
                $errors[] = 'El archivo es demasiado grande';
            } else {
                // Eliminar archivo anterior si existe
                if ($gasto['comprobante'] && file_exists(UPLOAD_PATH . $gasto['comprobante'])) {
                    unlink(UPLOAD_PATH . $gasto['comprobante']);
                }
                
                // Subir nuevo archivo
                $nombreArchivo = 'comprobante_' . uniqid() . '.' . $extension;
                $rutaDestino = UPLOAD_PATH . 'comprobantes/' . $nombreArchivo;
                
                if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                    $nombreArchivo = 'comprobantes/' . $nombreArchivo;
                } else {
                    $errors[] = 'Error al subir el archivo';
                    $nombreArchivo = $gasto['comprobante']; // Mantener el anterior
                }
            }
        }
        
        if (!empty($errors)) {
            $categoriaModel = $this->loadModel('Categoria');
            $metodoModel = $this->loadModel('MetodoPago');
            $categorias = $categoriaModel->getCategorias();
            $metodosPago = $metodoModel->getMetodosActivos();
            
            $this->loadView('gastos/edit', [
                'errors' => $errors,
                'csrf_token' => $this->csrf(),
                'title' => 'Editar Gasto',
                'gasto' => array_merge($gasto, [
                    'monto' => $monto,
                    'descripcion' => $descripcion,
                    'fecha_gasto' => $fecha_gasto,
                    'categoria_id' => $categoria_id,
                    'metodo_pago_id' => $metodo_pago_id
                ]),
                'categorias' => $categorias,
                'metodosPago' => $metodosPago
            ]);
            return;
        }
        
        // Actualizar gasto
        $success = $gastoModel->update($id, [
            'categoria_id' => $categoria_id,
            'metodo_pago_id' => $metodo_pago_id,
            'monto' => $monto,
            'descripcion' => $descripcion,
            'fecha_gasto' => $fecha_gasto,
            'comprobante' => $nombreArchivo
        ]);
        
        if ($success) {
            $this->redirect('/gastos?success=Gasto actualizado exitosamente');
        } else {
            $categoriaModel = $this->loadModel('Categoria');
            $metodoModel = $this->loadModel('MetodoPago');
            $categorias = $categoriaModel->getCategorias();
            $metodosPago = $metodoModel->getMetodosActivos();
            
            $this->loadView('gastos/edit', [
                'error' => 'Error al actualizar gasto',
                'csrf_token' => $this->csrf(),
                'title' => 'Editar Gasto',
                'gasto' => $gasto,
                'categorias' => $categorias,
                'metodosPago' => $metodosPago
            ]);
        }
    }
    
    public function delete($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/gastos?error=Método no permitido');
        }
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrf($csrf_token)) {
            $this->redirect('/gastos?error=Token de seguridad inválido');
        }
        
        $gastoModel = $this->loadModel('Gasto');
        $success = $gastoModel->eliminarGasto($id, $_SESSION['user_id']);
        
        if ($success) {
            $this->redirect('/gastos?success=Gasto eliminado exitosamente');
        } else {
            $this->redirect('/gastos?error=Error al eliminar gasto o gasto no encontrado');
        }
    }
}