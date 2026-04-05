# Plan de Trabajo: Estabilización y Mejoras - VehiculosQr

Este plan sirve como guía del agente para ir resolviendo los problemas y mejorando el código. Se irán marcando con un `[x]` a medida que se ejecuten.

## Fase 1: Corrección de Errores Críticos
- [ ] **1.1. Arreglar Crash en Logs (`/vehiculos/admin/logs`)**:
  - Verificar y analizar `module.config.php`. El enrutamiento de `vehiculos-admin-logs` está como `Literal` sin el segmento del ID, o bien en `AdminController.php` usar `fromQuery('uuid')`.
  - Preferible: Modificar la ruta a tipo `Segment` => `/vehiculos/admin/logs/:id` y añadir `id` de tipo string/uuid a los constraints.
- [ ] **1.2. Arreglar Fallo de Persistencia en Estado de Usuario**:
  - Revisar la petición de la vista (seguramente JS usando `fetch` o AJAX enviando JSON).
  - En `AdminController::cambiarEstadoUsuarioAction()`, asegurarse de parsear correctamente `$_POST` (o `$this->getRequest()->getContent()`) si los datos vienen como *payload JSON application/json* en lugar de formData.
  - Asegurar actualizacion en DB llamando a validaciones correctas de booleanos.

## Fase 2: Fixes Menores y de UX/UI
- [ ] **2.1. Ordenamiento y Búsqueda Global en DataTables**:
  - Examinar cómo está configurado DataTables en `gestion.phtml`. Actualmente, carga PHP-paginado pero inicia DataTables de manera local sobre esos 20 registros. Solución: Modificar a DataTables full Client-side (si son pocos datos, pasar todo sin LIMIT) o usar el verdadero Ajax Server-side rendering (más complejo). Como es panel de control, podemos quitar el LIMIT 20 y mandar toda la data, dejando que DataTables se encargue del paginado global en cliente (recomendado para < 2,000 QRs con poca sobrecarga). Alternativa: Dejar un buscar en DB global mediante PHP y mandar las consultas, pero se pierde UX. Optaremos por sacar la paginacion PHP y usar Server-Side/Client-Side integro en la tabla.
  - Corregir el formato de fecha (pasarla en `YYYY-MM-DD` en un tag `data-sort` u order de datatables para habilitar sorting cronológico).
- [ ] **2.2. Opciones de Eliminación de QR**:
  - Implementar en `AdminController` un `eliminarAction()` para borrarlos (soft delete: cambiar un flag de status a `ELIMINADO` o hard delete si es requerido). Agregar el botón rojo en las acciones de la tabla con confirmación simple.
  - Actualizar `QrService` y Repositorios.

## Fase 3: Pruebas y Cierre
- [ ] **3.1. Testing y Validación Global**:
  - Probar las rutas resultantes y verificar que Logs ya no lanza 500.
  - Probar inactivar un usuario de nuevo y validar DB.
  - Chequear el responsive de la tabla.
