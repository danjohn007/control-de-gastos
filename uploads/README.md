# Directorio de uploads

Este directorio almacena los archivos subidos por los usuarios (comprobantes, facturas, etc.).

## Permisos requeridos

```bash
chmod 777 uploads/
```

## Configuración de seguridad

- Solo se permiten archivos JPG, JPEG, PNG y PDF
- Tamaño máximo: 5MB por archivo
- Los archivos PHP están bloqueados por seguridad

## Estructura

```
uploads/
├── comprobantes/     # Comprobantes de gastos
├── avatars/          # Fotos de perfil (futuro)
└── temp/            # Archivos temporales
```