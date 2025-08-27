<?php
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-plus-circle text-primary"></i>
        Registrar Gasto
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="<?= BASE_URL ?>/gastos" class="btn btn-outline-secondary">
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
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-receipt"></i> Datos del Gasto
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/gastos/crear" 
                      enctype="multipart/form-data" id="gastoForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="monto" class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Monto *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="monto" name="monto" 
                                           value="<?= htmlspecialchars($monto ?? '') ?>" 
                                           step="0.01" min="0.01" max="999999.99" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_gasto" class="form-label">
                                    <i class="fas fa-calendar"></i> Fecha del Gasto *
                                </label>
                                <input type="date" class="form-control" id="fecha_gasto" name="fecha_gasto" 
                                       value="<?= htmlspecialchars($fecha_gasto ?? date('Y-m-d')) ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria_id" class="form-label">
                                    <i class="fas fa-tag"></i> Categoría *
                                </label>
                                <select class="form-select" id="categoria_id" name="categoria_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <?php if ($categoria['estado'] === 'activo'): ?>
                                            <option value="<?= $categoria['id'] ?>" 
                                                    data-color="<?= $categoria['color'] ?>"
                                                    data-icon="<?= $categoria['icono'] ?>"
                                                    <?= ($categoria_id ?? '') == $categoria['id'] ? 'selected' : '' ?>>
                                                <?php if ($categoria['categoria_padre_id']): ?>
                                                    &nbsp;&nbsp;&nbsp;└ 
                                                <?php endif; ?>
                                                <?= htmlspecialchars($categoria['nombre']) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="metodo_pago_id" class="form-label">
                                    <i class="fas fa-credit-card"></i> Método de Pago *
                                </label>
                                <select class="form-select" id="metodo_pago_id" name="metodo_pago_id" required>
                                    <option value="">Selecciona un método</option>
                                    <?php foreach ($metodosPago as $metodo): ?>
                                        <option value="<?= $metodo['id'] ?>" 
                                                <?= ($metodo_pago_id ?? '') == $metodo['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($metodo['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left"></i> Descripción
                        </label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                  rows="3" placeholder="Descripción opcional del gasto"><?= htmlspecialchars($descripcion ?? '') ?></textarea>
                        <div class="form-text">Puedes agregar detalles sobre el gasto</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="comprobante" class="form-label">
                            <i class="fas fa-file-upload"></i> Comprobante (Opcional)
                        </label>
                        <input type="file" class="form-control" id="comprobante" name="comprobante" 
                               accept=".jpg,.jpeg,.png,.pdf">
                        <div class="form-text">
                            Formatos permitidos: JPG, JPEG, PNG, PDF. Tamaño máximo: 5MB
                        </div>
                        <div id="comprobante_preview" class="mt-2"></div>
                    </div>
                    
                    <!-- Vista previa del gasto -->
                    <div class="mb-3" id="preview-container" style="display: none;">
                        <label class="form-label">Vista Previa</label>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span id="preview-icon" class="me-2" style="font-size: 1.5em;">
                                            <i class="fas fa-receipt"></i>
                                        </span>
                                        <div>
                                            <strong id="preview-categoria">Categoría</strong>
                                            <br>
                                            <small class="text-muted" id="preview-fecha"></small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="mb-0" id="preview-monto">$0.00</h5>
                                        <small class="text-muted" id="preview-metodo"></small>
                                    </div>
                                </div>
                                <div class="mt-2" id="preview-descripcion" style="display: none;">
                                    <small class="text-muted"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= BASE_URL ?>/gastos" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Registrar Gasto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Información adicional -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle"></i> Consejos
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        Registra tus gastos inmediatamente
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        Agrega una descripción clara
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        Adjunta el comprobante cuando sea posible
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i>
                        Usa categorías específicas para mejor análisis
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Gastos recientes -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-history"></i> Gastos Recientes
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Aquí aparecerán tus últimos gastos registrados para referencia rápida.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const montoInput = document.getElementById('monto');
    const fechaInput = document.getElementById('fecha_gasto');
    const categoriaSelect = document.getElementById('categoria_id');
    const metodoSelect = document.getElementById('metodo_pago_id');
    const descripcionInput = document.getElementById('descripcion');
    const previewContainer = document.getElementById('preview-container');
    
    // Elementos de vista previa
    const previewIcon = document.getElementById('preview-icon');
    const previewCategoria = document.getElementById('preview-categoria');
    const previewFecha = document.getElementById('preview-fecha');
    const previewMonto = document.getElementById('preview-monto');
    const previewMetodo = document.getElementById('preview-metodo');
    const previewDescripcion = document.getElementById('preview-descripcion');
    
    // Actualizar vista previa
    function updatePreview() {
        const monto = parseFloat(montoInput.value) || 0;
        const fecha = fechaInput.value;
        const categoriaOption = categoriaSelect.selectedOptions[0];
        const metodoOption = metodoSelect.selectedOptions[0];
        const descripcion = descripcionInput.value.trim();
        
        if (monto > 0 && fecha && categoriaOption && metodoOption) {
            previewContainer.style.display = 'block';
            
            // Actualizar icono y color
            if (categoriaOption) {
                const color = categoriaOption.dataset.color || '#007bff';
                const icon = categoriaOption.dataset.icon || 'fas fa-tag';
                previewIcon.innerHTML = `<i class="${icon}"></i>`;
                previewIcon.style.color = color;
                previewCategoria.textContent = categoriaOption.text.trim();
            }
            
            // Actualizar fecha
            if (fecha) {
                const fechaObj = new Date(fecha + 'T00:00:00');
                previewFecha.textContent = fechaObj.toLocaleDateString('es-MX');
            }
            
            // Actualizar monto
            previewMonto.textContent = App.formatCurrency ? App.formatCurrency(monto) : `$${monto.toFixed(2)}`;
            
            // Actualizar método
            if (metodoOption) {
                previewMetodo.textContent = metodoOption.text;
            }
            
            // Actualizar descripción
            if (descripcion) {
                previewDescripcion.style.display = 'block';
                previewDescripcion.querySelector('small').textContent = descripcion;
            } else {
                previewDescripcion.style.display = 'none';
            }
        } else {
            previewContainer.style.display = 'none';
        }
    }
    
    // Event listeners para vista previa
    montoInput.addEventListener('input', updatePreview);
    fechaInput.addEventListener('change', updatePreview);
    categoriaSelect.addEventListener('change', updatePreview);
    metodoSelect.addEventListener('change', updatePreview);
    descripcionInput.addEventListener('input', updatePreview);
    
    // Preview de archivo
    document.getElementById('comprobante').addEventListener('change', function() {
        const preview = document.getElementById('comprobante_preview');
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                if (file.type.startsWith('image/')) {
                    preview.innerHTML = `
                        <div class="border rounded p-2">
                            <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            <br><small class="text-muted">${file.name}</small>
                        </div>
                    `;
                } else {
                    preview.innerHTML = `
                        <div class="border rounded p-2">
                            <i class="fas fa-file-pdf fa-3x text-danger"></i>
                            <br><small class="text-muted">${file.name}</small>
                        </div>
                    `;
                }
            };
            
            reader.readAsDataURL(file);
        }
    });
    
    // Validación del formulario
    document.getElementById('gastoForm').addEventListener('submit', function(e) {
        if (!App.validateForm('gastoForm')) {
            e.preventDefault();
            App.showNotification('Por favor, completa todos los campos requeridos', 'warning');
            return false;
        }
        
        // Validación adicional del monto
        const monto = parseFloat(montoInput.value);
        if (monto <= 0) {
            e.preventDefault();
            App.showNotification('El monto debe ser mayor a 0', 'warning');
            return false;
        }
    });
    
    // Inicializar vista previa
    updatePreview();
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>