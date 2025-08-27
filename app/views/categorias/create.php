<?php
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-plus-circle text-primary"></i>
        Crear Categoría
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= BASE_URL ?>/categorias" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</div>

<!-- Mensajes de error -->
<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger">
        <h6><i class="fas fa-exclamation-triangle"></i> Se encontraron errores:</h6>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Formulario -->
<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tag"></i> Datos de la Categoría
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/categorias/crear" id="categoryForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-tag"></i> Nombre de la Categoría *
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($nombre ?? '') ?>" required maxlength="100">
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left"></i> Descripción
                        </label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                  rows="3"><?= htmlspecialchars($descripcion ?? '') ?></textarea>
                        <div class="form-text">Descripción opcional de la categoría</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">
                                    <i class="fas fa-palette"></i> Color *
                                </label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="color" name="color" value="<?= $color ?? '#007bff' ?>" required>
                                    <input type="text" class="form-control" id="colorText" 
                                           value="<?= $color ?? '#007bff' ?>" readonly>
                                </div>
                                <div class="form-text">Color para identificar la categoría</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icono" class="form-label">
                                    <i class="fas fa-icons"></i> Icono *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i id="iconPreview" class="<?= $icono ?? 'fas fa-tag' ?>"></i>
                                    </span>
                                    <select class="form-select" id="icono" name="icono" required>
                                        <option value="fas fa-tag" <?= ($icono ?? 'fas fa-tag') === 'fas fa-tag' ? 'selected' : '' ?>>Etiqueta</option>
                                        <option value="fas fa-utensils" <?= ($icono ?? '') === 'fas fa-utensils' ? 'selected' : '' ?>>Alimentación</option>
                                        <option value="fas fa-car" <?= ($icono ?? '') === 'fas fa-car' ? 'selected' : '' ?>>Transporte</option>
                                        <option value="fas fa-bolt" <?= ($icono ?? '') === 'fas fa-bolt' ? 'selected' : '' ?>>Servicios</option>
                                        <option value="fas fa-gamepad" <?= ($icono ?? '') === 'fas fa-gamepad' ? 'selected' : '' ?>>Ocio</option>
                                        <option value="fas fa-heart" <?= ($icono ?? '') === 'fas fa-heart' ? 'selected' : '' ?>>Salud</option>
                                        <option value="fas fa-graduation-cap" <?= ($icono ?? '') === 'fas fa-graduation-cap' ? 'selected' : '' ?>>Educación</option>
                                        <option value="fas fa-home" <?= ($icono ?? '') === 'fas fa-home' ? 'selected' : '' ?>>Hogar</option>
                                        <option value="fas fa-tshirt" <?= ($icono ?? '') === 'fas fa-tshirt' ? 'selected' : '' ?>>Ropa</option>
                                        <option value="fas fa-shopping-cart" <?= ($icono ?? '') === 'fas fa-shopping-cart' ? 'selected' : '' ?>>Compras</option>
                                        <option value="fas fa-pizza-slice" <?= ($icono ?? '') === 'fas fa-pizza-slice' ? 'selected' : '' ?>>Restaurante</option>
                                        <option value="fas fa-gas-pump" <?= ($icono ?? '') === 'fas fa-gas-pump' ? 'selected' : '' ?>>Combustible</option>
                                        <option value="fas fa-bus" <?= ($icono ?? '') === 'fas fa-bus' ? 'selected' : '' ?>>Transporte Público</option>
                                        <option value="fas fa-wrench" <?= ($icono ?? '') === 'fas fa-wrench' ? 'selected' : '' ?>>Mantenimiento</option>
                                        <option value="fas fa-film" <?= ($icono ?? '') === 'fas fa-film' ? 'selected' : '' ?>>Entretenimiento</option>
                                        <option value="fas fa-dumbbell" <?= ($icono ?? '') === 'fas fa-dumbbell' ? 'selected' : '' ?>>Deportes</option>
                                        <option value="fas fa-book" <?= ($icono ?? '') === 'fas fa-book' ? 'selected' : '' ?>>Libros</option>
                                        <option value="fas fa-phone" <?= ($icono ?? '') === 'fas fa-phone' ? 'selected' : '' ?>>Teléfono</option>
                                        <option value="fas fa-wifi" <?= ($icono ?? '') === 'fas fa-wifi' ? 'selected' : '' ?>>Internet</option>
                                        <option value="fas fa-piggy-bank" <?= ($icono ?? '') === 'fas fa-piggy-bank' ? 'selected' : '' ?>>Ahorros</option>
                                    </select>
                                </div>
                                <div class="form-text">Icono Font Awesome para la categoría</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria_padre_id" class="form-label">
                                    <i class="fas fa-folder"></i> Categoría Padre
                                </label>
                                <select class="form-select" id="categoria_padre_id" name="categoria_padre_id">
                                    <option value="">Es una categoría principal</option>
                                    <?php foreach ($categoriasPrincipales as $categoriaPrincipal): ?>
                                        <option value="<?= $categoriaPrincipal['id'] ?>" 
                                                <?= ($categoria_padre_id ?? '') == $categoriaPrincipal['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($categoriaPrincipal['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Dejar vacío para crear una categoría principal</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">
                                    <i class="fas fa-toggle-on"></i> Estado *
                                </label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="activo" <?= ($estado ?? 'activo') === 'activo' ? 'selected' : '' ?>>
                                        Activo
                                    </option>
                                    <option value="inactivo" <?= ($estado ?? '') === 'inactivo' ? 'selected' : '' ?>>
                                        Inactivo
                                    </option>
                                </select>
                                <div class="form-text">Solo categorías activas aparecen en los formularios</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vista previa -->
                    <div class="mb-3">
                        <label class="form-label">Vista Previa</label>
                        <div class="p-3 border rounded" id="preview" 
                             style="background: linear-gradient(45deg, #007bff22, transparent);">
                            <span id="previewIcon" class="me-2" style="color: #007bff; font-size: 1.5em;">
                                <i class="fas fa-tag"></i>
                            </span>
                            <span id="previewName" class="fw-bold">Nombre de la categoría</span>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= BASE_URL ?>/categorias" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Información adicional -->
    <div class="col-md-4 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i> Información sobre Categorías
                </h6>
            </div>
            <div class="card-body">
                <h6>Tipos de categorías:</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <span class="badge bg-primary me-2">Principal</span>
                        Categoría independiente
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-info me-2">Subcategoría</span>
                        Pertenece a una categoría principal
                    </li>
                </ul>
                
                <hr>
                
                <h6>Consejos:</h6>
                <ul class="small">
                    <li>Usa nombres descriptivos y cortos</li>
                    <li>Elige colores distintivos</li>
                    <li>Los iconos ayudan a identificar rápidamente</li>
                    <li>Las subcategorías no pueden tener subcategorías</li>
                </ul>
            </div>
        </div>
        
        <!-- Categorías existentes -->
        <?php if (!empty($categoriasPrincipales)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-list"></i> Categorías Principales Existentes
                    </h6>
                </div>
                <div class="card-body">
                    <?php foreach ($categoriasPrincipales as $cat): ?>
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2" style="color: <?= $cat['color'] ?>">
                                <i class="<?= $cat['icono'] ?>"></i>
                            </span>
                            <span><?= htmlspecialchars($cat['nombre']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('colorText');
    const iconoSelect = document.getElementById('icono');
    const iconPreview = document.getElementById('iconPreview');
    const nombreInput = document.getElementById('nombre');
    const preview = document.getElementById('preview');
    const previewIcon = document.getElementById('previewIcon');
    const previewName = document.getElementById('previewName');
    
    // Actualizar vista previa
    function updatePreview() {
        const color = colorInput.value;
        const icono = iconoSelect.value;
        const nombre = nombreInput.value || 'Nombre de la categoría';
        
        // Actualizar color del texto
        colorText.value = color;
        
        // Actualizar icono
        iconPreview.className = icono;
        
        // Actualizar vista previa
        preview.style.background = `linear-gradient(45deg, ${color}22, transparent)`;
        previewIcon.style.color = color;
        previewIcon.innerHTML = `<i class="${icono}"></i>`;
        previewName.textContent = nombre;
    }
    
    // Event listeners
    colorInput.addEventListener('input', updatePreview);
    iconoSelect.addEventListener('change', updatePreview);
    nombreInput.addEventListener('input', updatePreview);
    
    // Inicializar vista previa
    updatePreview();
    
    // Validación del formulario
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        if (!App.validateForm('categoryForm')) {
            e.preventDefault();
            App.showNotification('Por favor, completa todos los campos requeridos', 'warning');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>