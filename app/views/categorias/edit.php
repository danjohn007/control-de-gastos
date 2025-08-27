<?php
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-edit text-primary"></i>
        Editar Categoría
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
                <form method="POST" action="<?= BASE_URL ?>/categorias/<?= $categoria['id'] ?>/editar" id="categoryForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">
                            <i class="fas fa-tag"></i> Nombre de la Categoría *
                        </label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($categoria['nombre']) ?>" required maxlength="100">
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left"></i> Descripción
                        </label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                  rows="3"><?= htmlspecialchars($categoria['descripcion'] ?? '') ?></textarea>
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
                                           id="color" name="color" value="<?= $categoria['color'] ?>" required>
                                    <input type="text" class="form-control" id="colorText" 
                                           value="<?= $categoria['color'] ?>" readonly>
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
                                        <i id="iconPreview" class="<?= $categoria['icono'] ?>"></i>
                                    </span>
                                    <select class="form-select" id="icono" name="icono" required>
                                        <option value="fas fa-tag" <?= $categoria['icono'] === 'fas fa-tag' ? 'selected' : '' ?>>Etiqueta</option>
                                        <option value="fas fa-utensils" <?= $categoria['icono'] === 'fas fa-utensils' ? 'selected' : '' ?>>Alimentación</option>
                                        <option value="fas fa-car" <?= $categoria['icono'] === 'fas fa-car' ? 'selected' : '' ?>>Transporte</option>
                                        <option value="fas fa-bolt" <?= $categoria['icono'] === 'fas fa-bolt' ? 'selected' : '' ?>>Servicios</option>
                                        <option value="fas fa-gamepad" <?= $categoria['icono'] === 'fas fa-gamepad' ? 'selected' : '' ?>>Ocio</option>
                                        <option value="fas fa-heart" <?= $categoria['icono'] === 'fas fa-heart' ? 'selected' : '' ?>>Salud</option>
                                        <option value="fas fa-graduation-cap" <?= $categoria['icono'] === 'fas fa-graduation-cap' ? 'selected' : '' ?>>Educación</option>
                                        <option value="fas fa-home" <?= $categoria['icono'] === 'fas fa-home' ? 'selected' : '' ?>>Hogar</option>
                                        <option value="fas fa-tshirt" <?= $categoria['icono'] === 'fas fa-tshirt' ? 'selected' : '' ?>>Ropa</option>
                                        <option value="fas fa-shopping-cart" <?= $categoria['icono'] === 'fas fa-shopping-cart' ? 'selected' : '' ?>>Compras</option>
                                        <option value="fas fa-pizza-slice" <?= $categoria['icono'] === 'fas fa-pizza-slice' ? 'selected' : '' ?>>Restaurante</option>
                                        <option value="fas fa-gas-pump" <?= $categoria['icono'] === 'fas fa-gas-pump' ? 'selected' : '' ?>>Combustible</option>
                                        <option value="fas fa-bus" <?= $categoria['icono'] === 'fas fa-bus' ? 'selected' : '' ?>>Transporte Público</option>
                                        <option value="fas fa-wrench" <?= $categoria['icono'] === 'fas fa-wrench' ? 'selected' : '' ?>>Mantenimiento</option>
                                        <option value="fas fa-film" <?= $categoria['icono'] === 'fas fa-film' ? 'selected' : '' ?>>Entretenimiento</option>
                                        <option value="fas fa-dumbbell" <?= $categoria['icono'] === 'fas fa-dumbbell' ? 'selected' : '' ?>>Deportes</option>
                                        <option value="fas fa-book" <?= $categoria['icono'] === 'fas fa-book' ? 'selected' : '' ?>>Libros</option>
                                        <option value="fas fa-phone" <?= $categoria['icono'] === 'fas fa-phone' ? 'selected' : '' ?>>Teléfono</option>
                                        <option value="fas fa-wifi" <?= $categoria['icono'] === 'fas fa-wifi' ? 'selected' : '' ?>>Internet</option>
                                        <option value="fas fa-piggy-bank" <?= $categoria['icono'] === 'fas fa-piggy-bank' ? 'selected' : '' ?>>Ahorros</option>
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
                                        <?php if ($categoriaPrincipal['id'] != $categoria['id']): // No puede ser padre de sí misma ?>
                                            <option value="<?= $categoriaPrincipal['id'] ?>" 
                                                    <?= $categoria['categoria_padre_id'] == $categoriaPrincipal['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoriaPrincipal['nombre']) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Dejar vacío para mantener como categoría principal</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">
                                    <i class="fas fa-toggle-on"></i> Estado *
                                </label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="activo" <?= $categoria['estado'] === 'activo' ? 'selected' : '' ?>>
                                        Activo
                                    </option>
                                    <option value="inactivo" <?= $categoria['estado'] === 'inactivo' ? 'selected' : '' ?>>
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
                             style="background: linear-gradient(45deg, <?= $categoria['color'] ?>22, transparent);">
                            <span id="previewIcon" class="me-2" style="color: <?= $categoria['color'] ?>; font-size: 1.5em;">
                                <i class="<?= $categoria['icono'] ?>"></i>
                            </span>
                            <span id="previewName" class="fw-bold"><?= htmlspecialchars($categoria['nombre']) ?></span>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= BASE_URL ?>/categorias" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Información de la categoría -->
    <div class="col-md-4 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i> Información de la Categoría
                </h6>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">ID:</dt>
                    <dd class="col-sm-8"><?= $categoria['id'] ?></dd>
                    
                    <dt class="col-sm-4">Tipo:</dt>
                    <dd class="col-sm-8">
                        <?php if ($categoria['categoria_padre_id']): ?>
                            <span class="badge bg-info">Subcategoría</span>
                        <?php else: ?>
                            <span class="badge bg-primary">Principal</span>
                        <?php endif; ?>
                    </dd>
                    
                    <dt class="col-sm-4">Creada:</dt>
                    <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($categoria['fecha_creacion'])) ?></dd>
                    
                    <dt class="col-sm-4">Actualizada:</dt>
                    <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($categoria['fecha_actualizacion'])) ?></dd>
                </dl>
            </div>
        </div>
        
        <!-- Advertencias -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle text-warning"></i> Advertencias
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <ul class="mb-0 small">
                        <li>Cambiar el estado a "Inactivo" ocultará la categoría de los formularios</li>
                        <li>No se puede eliminar una categoría con gastos asociados</li>
                        <li>Las subcategorías no pueden tener subcategorías</li>
                    </ul>
                </div>
            </div>
        </div>
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