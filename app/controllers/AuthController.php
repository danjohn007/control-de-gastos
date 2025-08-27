<?php
require_once __DIR__ . '/../controllers/Controller.php';

/**
 * Controlador de autenticación
 */
class AuthController extends Controller {
    
    public function login() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            $this->loadView('auth/login', [
                'csrf_token' => $this->csrf(),
                'title' => 'Iniciar Sesión'
            ]);
        }
    }
    
    private function processLogin() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        // Validar CSRF
        if (!$this->validateCsrf($csrf_token)) {
            $this->loadView('auth/login', [
                'error' => 'Token de seguridad inválido',
                'csrf_token' => $this->csrf(),
                'title' => 'Iniciar Sesión'
            ]);
            return;
        }
        
        // Validaciones básicas
        if (empty($email) || empty($password)) {
            $this->loadView('auth/login', [
                'error' => 'Email y contraseña son requeridos',
                'csrf_token' => $this->csrf(),
                'title' => 'Iniciar Sesión',
                'email' => $email
            ]);
            return;
        }
        
        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->loadView('auth/login', [
                'error' => 'Email inválido',
                'csrf_token' => $this->csrf(),
                'title' => 'Iniciar Sesión',
                'email' => $email
            ]);
            return;
        }
        
        // Intentar autenticar
        $userModel = $this->loadModel('Usuario');
        $user = $userModel->authenticate($email, $password);
        
        if ($user) {
            // Inicializar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['rol'];
            $_SESSION['login_time'] = time();
            
            $this->redirect('/dashboard');
        } else {
            $this->loadView('auth/login', [
                'error' => 'Credenciales incorrectas',
                'csrf_token' => $this->csrf(),
                'title' => 'Iniciar Sesión',
                'email' => $email
            ]);
        }
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
    
    public function register() {
        // Solo para usuarios no autenticados
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processRegister();
        } else {
            $this->loadView('auth/register', [
                'csrf_token' => $this->csrf(),
                'title' => 'Registro'
            ]);
        }
    }
    
    private function processRegister() {
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
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
        
        // Verificar si el email ya existe
        if (empty($errors)) {
            $userModel = $this->loadModel('Usuario');
            if ($userModel->emailExists($email)) {
                $errors[] = 'El email ya está registrado';
            }
        }
        
        if (!empty($errors)) {
            $this->loadView('auth/register', [
                'errors' => $errors,
                'csrf_token' => $this->csrf(),
                'title' => 'Registro',
                'nombre' => $nombre,
                'email' => $email
            ]);
            return;
        }
        
        // Crear usuario
        $userModel = $this->loadModel('Usuario');
        $userId = $userModel->createUser([
            'nombre' => $nombre,
            'email' => $email,
            'password' => $password,
            'rol' => 'usuario'
        ]);
        
        if ($userId) {
            $this->loadView('auth/login', [
                'success' => 'Usuario registrado exitosamente. Puede iniciar sesión.',
                'csrf_token' => $this->csrf(),
                'title' => 'Iniciar Sesión',
                'email' => $email
            ]);
        } else {
            $this->loadView('auth/register', [
                'error' => 'Error al registrar usuario',
                'csrf_token' => $this->csrf(),
                'title' => 'Registro',
                'nombre' => $nombre,
                'email' => $email
            ]);
        }
    }
    
    public function checkSession() {
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_TIMEOUT) {
            session_destroy();
            $this->redirect('/login?expired=1');
        }
    }
}