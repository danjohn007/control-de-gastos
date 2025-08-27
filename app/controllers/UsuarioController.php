<?php
require_once __DIR__ . '/Controller.php';

/**
 * Controlador de Usuarios (solo para administradores)
 */
class UsuarioController extends Controller {
    
    public function index() {
        $this->requireAdmin();
        
        $userModel = $this->loadModel('Usuario');
        $usuarios = $userModel->getAllUsers();
        
        $this->loadView('usuarios/index', [
            'title' => 'Gestión de Usuarios',
            'usuarios' => $usuarios,
            'csrf_token' => $this->csrf()
        ]);
    }
    
    public function create() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
        } else {
            $this->loadView('usuarios/create', [
                'title' => 'Crear Usuario',
                'csrf_token' => $this->csrf()
            ]);
        }
    }
    
    public function store() {
        $this->requireAdmin();
        
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $rol = $_POST['rol'] ?? 'usuario';
        $estado = $_POST['estado'] ?? 'activo';
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        $errors = [];
        
        // Validar CSRF
        if (!$this->validateCsrf($csrf_token)) {
            $errors[] = 'Token de seguridad inválido';
        }
        
        // Validaciones
        if (empty($nombre)) $errors[] = 'El nombre es requerido';
        if (empty($email)) $errors[] = 'El email es requerido';
        if (empty($password)) $errors[] = 'La contraseña es requerida';
        if ($password !== $confirmPassword) $errors[] = 'Las contraseñas no coinciden';
        if (strlen($password) < 6) $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
        if (!in_array($rol, ['admin', 'usuario'])) $errors[] = 'Rol inválido';
        if (!in_array($estado, ['activo', 'inactivo'])) $errors[] = 'Estado inválido';
        
        // Verificar si el email ya existe
        if (empty($errors)) {
            $userModel = $this->loadModel('Usuario');
            if ($userModel->emailExists($email)) {
                $errors[] = 'El email ya está registrado';
            }
        }
        
        if (!empty($errors)) {
            $this->loadView('usuarios/create', [
                'errors' => $errors,
                'csrf_token' => $this->csrf(),
                'title' => 'Crear Usuario',
                'nombre' => $nombre,
                'email' => $email,
                'rol' => $rol,
                'estado' => $estado
            ]);
            return;
        }
        
        // Crear usuario
        $userModel = $this->loadModel('Usuario');
        $userId = $userModel->createUser([
            'nombre' => $nombre,
            'email' => $email,
            'password' => $password,
            'rol' => $rol,
            'estado' => $estado
        ]);
        
        if ($userId) {
            $this->redirect('/usuarios?success=Usuario creado exitosamente');
        } else {
            $this->loadView('usuarios/create', [
                'error' => 'Error al crear usuario',
                'csrf_token' => $this->csrf(),
                'title' => 'Crear Usuario',
                'nombre' => $nombre,
                'email' => $email,
                'rol' => $rol,
                'estado' => $estado
            ]);
        }
    }
    
    public function edit($id) {
        $this->requireAdmin();
        
        $userModel = $this->loadModel('Usuario');
        $usuario = $userModel->find($id);
        
        if (!$usuario) {
            $this->redirect('/usuarios?error=Usuario no encontrado');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
        } else {
            $this->loadView('usuarios/edit', [
                'title' => 'Editar Usuario',
                'usuario' => $usuario,
                'csrf_token' => $this->csrf()
            ]);
        }
    }
    
    public function update($id) {
        $this->requireAdmin();
        
        $userModel = $this->loadModel('Usuario');
        $usuario = $userModel->find($id);
        
        if (!$usuario) {
            $this->redirect('/usuarios?error=Usuario no encontrado');
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $rol = $_POST['rol'] ?? 'usuario';
        $estado = $_POST['estado'] ?? 'activo';
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        $errors = [];
        
        // Validar CSRF
        if (!$this->validateCsrf($csrf_token)) {
            $errors[] = 'Token de seguridad inválido';
        }
        
        // Validaciones
        if (empty($nombre)) $errors[] = 'El nombre es requerido';
        if (empty($email)) $errors[] = 'El email es requerido';
        if (!empty($password) && $password !== $confirmPassword) $errors[] = 'Las contraseñas no coinciden';
        if (!empty($password) && strlen($password) < 6) $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
        if (!in_array($rol, ['admin', 'usuario'])) $errors[] = 'Rol inválido';
        if (!in_array($estado, ['activo', 'inactivo'])) $errors[] = 'Estado inválido';
        
        // Verificar si el email ya existe (excluyendo el usuario actual)
        if (empty($errors)) {
            if ($userModel->emailExists($email, $id)) {
                $errors[] = 'El email ya está registrado';
            }
        }
        
        if (!empty($errors)) {
            $this->loadView('usuarios/edit', [
                'errors' => $errors,
                'csrf_token' => $this->csrf(),
                'title' => 'Editar Usuario',
                'usuario' => array_merge($usuario, [
                    'nombre' => $nombre,
                    'email' => $email,
                    'rol' => $rol,
                    'estado' => $estado
                ])
            ]);
            return;
        }
        
        // Actualizar usuario
        $updateData = [
            'nombre' => $nombre,
            'email' => $email,
            'rol' => $rol,
            'estado' => $estado
        ];
        
        // Solo actualizar contraseña si se proporcionó una nueva
        if (!empty($password)) {
            $updateData['password'] = $password;
        }
        
        $success = $userModel->updateUser($id, $updateData);
        
        if ($success) {
            $this->redirect('/usuarios?success=Usuario actualizado exitosamente');
        } else {
            $this->loadView('usuarios/edit', [
                'error' => 'Error al actualizar usuario',
                'csrf_token' => $this->csrf(),
                'title' => 'Editar Usuario',
                'usuario' => array_merge($usuario, $updateData)
            ]);
        }
    }
    
    public function delete($id) {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios?error=Método no permitido');
        }
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrf($csrf_token)) {
            $this->redirect('/usuarios?error=Token de seguridad inválido');
        }
        
        $userModel = $this->loadModel('Usuario');
        $usuario = $userModel->find($id);
        
        if (!$usuario) {
            $this->redirect('/usuarios?error=Usuario no encontrado');
        }
        
        // No permitir eliminar el usuario actual
        if ($usuario['id'] == $_SESSION['user_id']) {
            $this->redirect('/usuarios?error=No puedes eliminar tu propio usuario');
        }
        
        // Verificar si el usuario tiene gastos asociados
        $gastoModel = $this->loadModel('Gasto');
        $gastos = $gastoModel->count(['usuario_id' => $id]);
        
        if ($gastos > 0) {
            $this->redirect('/usuarios?error=No se puede eliminar un usuario con gastos registrados');
        }
        
        $success = $userModel->delete($id);
        
        if ($success) {
            $this->redirect('/usuarios?success=Usuario eliminado exitosamente');
        } else {
            $this->redirect('/usuarios?error=Error al eliminar usuario');
        }
    }
}