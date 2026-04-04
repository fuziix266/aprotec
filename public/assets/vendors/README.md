# 📦 Vendors - Librerías de Terceros

Este directorio contiene todas las librerías externas descargadas localmente para evitar dependencias de CDN.

## 📚 Librerías Incluidas

### Bootstrap 5.3.2
- **Ubicación:** `bootstrap/`
- **Archivos:**
  - `css/bootstrap.min.css` - CSS principal de Bootstrap
  - `js/bootstrap.bundle.min.js` - JavaScript de Bootstrap (incluye Popper.js)
- **Sitio oficial:** https://getbootstrap.com/
- **Licencia:** MIT

### Bootstrap Icons 1.11.1
- **Ubicación:** `bootstrap-icons/`
- **Archivos:**
  - `bootstrap-icons.min.css` - CSS de iconos
  - `fonts/bootstrap-icons.woff2` - Fuente de iconos
- **Sitio oficial:** https://icons.getbootstrap.com/
- **Licencia:** MIT

### SweetAlert2 v11
- **Ubicación:** `sweetalert2/`
- **Archivos:**
  - `sweetalert2.min.css` - Estilos de SweetAlert2
  - `sweetalert2.all.min.js` - JavaScript completo con estilos incluidos
- **Sitio oficial:** https://sweetalert2.github.io/
- **Licencia:** MIT

### jQuery 3.7.1
- **Ubicación:** `jquery/`
- **Archivos:**
  - `jquery-3.7.1.min.js` - Biblioteca jQuery
- **Sitio oficial:** https://jquery.com/
- **Licencia:** MIT

### DataTables 1.13.7
- **Ubicación:** `datatables/`
- **Archivos CSS:**
  - `css/dataTables.bootstrap5.min.css` - Integración con Bootstrap 5
  - `css/responsive.bootstrap5.min.css` - Responsive para Bootstrap 5
- **Archivos JavaScript:**
  - `js/jquery.dataTables.min.js` - Core de DataTables
  - `js/dataTables.bootstrap5.min.js` - Integración Bootstrap 5
  - `js/dataTables.responsive.min.js` - Plugin Responsive
  - `js/responsive.bootstrap5.min.js` - Responsive Bootstrap 5
  - `js/es-ES.json` - Traducción al español
- **Sitio oficial:** https://datatables.net/
- **Licencia:** MIT

## 🔄 Actualización de Librerías

Para actualizar cualquier librería:

1. Visitar el sitio oficial de la librería
2. Descargar la versión más reciente
3. Reemplazar los archivos en la carpeta correspondiente
4. Actualizar este README con la nueva versión
5. Probar que todo funcione correctamente

## 📝 Notas

- Todos los archivos están minificados (.min) para mejor rendimiento
- Las fuentes de Bootstrap Icons se cargan automáticamente desde la carpeta `fonts/`
- DataTables incluye traducción al español (es-ES.json)
- SweetAlert2 usa el archivo `.all` que incluye CSS y JS juntos

## ✅ Verificación

Para verificar que todos los archivos estén presentes:

```bash
# En PowerShell, desde la raíz del proyecto
Get-ChildItem -Recurse -Path public\vehiculos\vendors\
```

---

**Última actualización:** 12 de noviembre de 2025
**Sistema:** QR Vehículos Municipales - Municipalidad de Arica
