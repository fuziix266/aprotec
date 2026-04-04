# 🖼️ Gestión de Imágenes

## 📁 Estructura

```
public/vehiculos/img/
├── escudo-arica-placeholder.svg  (Logo placeholder temporal)
└── (Aquí irá tu logo oficial)
```

## 🔄 Cómo Reemplazar el Logo

### Opción 1: Usando tu propio archivo

1. **Preparar el logo:**
   - Formato recomendado: PNG o SVG
   - Tamaño recomendado: 200x200px mínimo
   - Fondo transparente preferiblemente

2. **Subir el archivo:**
   - Copia tu archivo de logo a: `public/vehiculos/img/`
   - Ejemplo: `public/vehiculos/img/escudo-arica-oficial.png`

3. **Actualizar el layout:**
   - Abrir: `module/Application/view/layout/layout.phtml`
   - Buscar las líneas que contienen: `escudo-arica-placeholder.svg`
   - Reemplazar con la ruta de tu logo:

```php
<!-- En el header (línea ~73) -->
<img src="/vehiculos/public/vehiculos/img/TU_LOGO_AQUI.png" 
     alt="Logo Municipalidad de Arica" 
     class="logo-img">

<!-- En el footer (línea ~186) -->
<img src="/vehiculos/public/vehiculos/img/TU_LOGO_AQUI.png" 
     alt="Logo Municipalidad" 
     class="footer-logo">
```

### Opción 2: Descargar el logo oficial

Si tienes acceso al logo oficial de la Municipalidad de Arica:

```powershell
# Desde la raíz del proyecto
Invoke-WebRequest -Uri "URL_DEL_LOGO_OFICIAL" -OutFile "public\vehiculos\img\escudo-arica-oficial.png"
```

## 🎨 Ajustes de Tamaño

Los estilos CSS ya están configurados en el layout:

### Header:
- Clase: `logo-img`
- Tamaño: 60px de alto (ajustable)
- Fondo blanco con border-radius
- Padding de 5px

### Footer:
- Clase: `footer-logo`
- Tamaño: 50px máximo de alto
- Opacidad: 0.8

### Personalización adicional:

Si necesitas ajustar el tamaño, modifica en `layout.phtml` dentro de la etiqueta `<style>`:

```css
.header-municipal .logo-img {
    height: 60px;  /* Cambiar aquí */
    width: auto;
    background: white;
    border-radius: 8px;
    padding: 5px;
}

.footer-municipal .footer-logo {
    max-height: 50px;  /* Cambiar aquí */
    opacity: 0.8;
}
```

## 📝 Formatos Soportados

- ✅ **PNG** - Recomendado para logos con transparencia
- ✅ **SVG** - Ideal para escalabilidad
- ✅ **JPG** - Para fotos, no recomendado para logos
- ✅ **WEBP** - Moderno y eficiente

## 🔍 Logo Actual

**Archivo en uso:** `escudo-arica-placeholder.svg`  
**Tipo:** SVG generado temporalmente  
**Descripción:** Logo placeholder azul con escudo simplificado y texto "ARICA"

## ✅ Checklist para Cambiar Logo

- [ ] Tener archivo del logo oficial
- [ ] Copiar archivo a `public/vehiculos/img/`
- [ ] Actualizar ruta en header (línea ~73)
- [ ] Actualizar ruta en footer (línea ~186)
- [ ] Verificar que se vea bien en navegador
- [ ] Probar en móvil (responsive)
- [ ] Guardar cambios y commit

---

**Nota:** El placeholder actual es completamente funcional y se verá bien mientras consigues el logo oficial.

**Última actualización:** 12 de noviembre de 2025
