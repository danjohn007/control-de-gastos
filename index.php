<?php
/**
 * Punto de entrada principal de la aplicación
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Cargar clases principales
require_once __DIR__ . '/app/models/Database.php';
require_once __DIR__ . '/app/models/Model.php';
require_once __DIR__ . '/app/controllers/Controller.php';

// Función de autoload para modelos y controladores
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/app/models/' . $class . '.php',
        __DIR__ . '/app/controllers/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Router simple
class Router {
    private $routes = [];
    
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function route($method, $uri) {
        // Limpiar URI
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if (empty($uri)) $uri = '/';
        
        // Buscar ruta exacta
        if (isset($this->routes[$method][$uri])) {
            $callback = $this->routes[$method][$uri];
            $this->executeCallback($callback);
            return;
        }
        
        // Buscar rutas con parámetros
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remover el match completo
                $this->executeCallback($callback, $matches);
                return;
            }
        }
        
        // Ruta no encontrada
        $this->notFound();
    }
    
    private function executeCallback($callback, $params = []) {
        if (is_callable($callback)) {
            call_user_func_array($callback, $params);
        } elseif (is_string($callback)) {
            list($controller, $method) = explode('@', $callback);
            $controllerInstance = new $controller();
            call_user_func_array([$controllerInstance, $method], $params);
        }
    }
    
    private function notFound() {
        http_response_code(404);
        include __DIR__ . '/app/views/errors/404.php';
    }
}

// Inicializar router
$router = new Router();

// Definir rutas
$router->get('/', function() {
    header('Location: ' . BASE_URL . '/dashboard');
});

$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@register');

$router->get('/dashboard', 'DashboardController@index');

// Rutas de usuarios (solo admin)
$router->get('/usuarios', 'UsuarioController@index');
$router->get('/usuarios/crear', 'UsuarioController@create');
$router->post('/usuarios/crear', 'UsuarioController@store');
$router->get('/usuarios/{id}/editar', 'UsuarioController@edit');
$router->post('/usuarios/{id}/editar', 'UsuarioController@update');
$router->post('/usuarios/{id}/eliminar', 'UsuarioController@delete');

// Rutas de categorías (solo admin)
$router->get('/categorias', 'CategoriaController@index');
$router->get('/categorias/crear', 'CategoriaController@create');
$router->post('/categorias/crear', 'CategoriaController@store');
$router->get('/categorias/{id}/editar', 'CategoriaController@edit');
$router->post('/categorias/{id}/editar', 'CategoriaController@update');
$router->post('/categorias/{id}/eliminar', 'CategoriaController@delete');

// Rutas de gastos
$router->get('/gastos', 'GastoController@index');
$router->get('/gastos/crear', 'GastoController@create');
$router->post('/gastos/crear', 'GastoController@store');
$router->get('/gastos/{id}/editar', 'GastoController@edit');
$router->post('/gastos/{id}/editar', 'GastoController@update');
$router->post('/gastos/{id}/eliminar', 'GastoController@delete');

// Rutas de reportes
$router->get('/reportes', 'ReporteController@index');
$router->get('/reportes/pdf', 'ReporteController@pdf');
$router->get('/reportes/excel', 'ReporteController@excel');
$router->get('/reportes/csv', 'ReporteController@csv');

// Rutas de presupuestos
$router->get('/presupuestos', 'PresupuestoController@index');
$router->get('/presupuestos/crear', 'PresupuestoController@create');
$router->post('/presupuestos/crear', 'PresupuestoController@store');
$router->get('/presupuestos/{id}/editar', 'PresupuestoController@edit');
$router->post('/presupuestos/{id}/editar', 'PresupuestoController@update');
$router->post('/presupuestos/{id}/eliminar', 'PresupuestoController@delete');

// Rutas API para AJAX
$router->get('/api/gastos/stats', 'ApiController@gastosStats');
$router->get('/api/categorias/gastos', 'ApiController@gastosPorCategoria');
$router->get('/api/presupuestos/alertas', 'ApiController@alertasPresupuesto');

// Verificar sesión en rutas protegidas
$protectedRoutes = ['/dashboard', '/usuarios', '/categorias', '/gastos', '/reportes', '/presupuestos', '/api'];
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

foreach ($protectedRoutes as $protected) {
    if (strpos($currentPath, $protected) === 0) {
        $authController = new AuthController();
        $authController->checkSession();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        break;
    }
}

// Procesar ruta
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->route($method, $uri);