# Informe de Análisis y Auditoría: Módulo Vehiculos

## 1. Resumen Ejecutivo

El módulo **Vehiculos** permite la gestión, registro de datos y generación de códigos QR (en PDF) para la validación de vehículos de la Municipalidad de Arica. Tras las integraciones y un análisis profundo mediante un agente de navegación, se ha comprobado un estado funcional avanzado pero con ciertas deficiencias críticas y oportunidades de mejora, tanto en el Backend (Laminas MVC) como en la UX/UI.

## 2. Hallazgos Funcionales (Bugs y Errores)

### 2.1 Error Crítico 500 en Panel de Logs

- **Descripción**: La ruta `/vehiculos/admin/logs` arroja un Internal Server Error (Error 500).
- **Causa Analizada**: El controlador `AdminController::logsAction()` requiere el parámetro `id` proveniente de la ruta (`fromRoute('id')`). Sin embargo, el endpoint está definido como de tipo `Literal` (sin wildcard `:id`), por lo que ese parámetro llega como `null`, ocasionando un `TypeError` en `$this->qrService->buscarPorUuid()`.
- **Ruta Afectada**: `/vehiculos/admin/logs`

### 2.2 Inconsistencia en la Paginación y Filtrado del Lado del Cliente (DataTables)

- **Falla en el Buscador**: El buscador nativo de la tabla de listado QR (usualmente provisto por un plugin Javascript) no es capaz de ubicar registros insertados en páginas subsiguientes si se usa renderizado desde servidor de forma mixta (se envían registros de a 20 por vez pero con UI de tabla simple).
- **Error en Ordenamiento**: La columna "Creado" o fecha de la tabla no obedece a un formato cronológico ISO adecuado. Trata a las fechas como cadenas ("DD/MM/YYYY"), causando un ordenamiento equivocado.

### 2.3 Problemas en la Gestión de Usuarios (Fallo de Persistencia)

- **Descripción**: Al hacer clic en un usuario para desactivarlo ("Desactivar"), la UI muestra el modal de confirmación, pero la acción es revertida, es decir, el usuario se mantiene en estado "Activo".
- **Causa Probable**: La petición AJAX probablemente se envía en un formato de Payload (JSON) y el controlador usa `$this->getRequest()->getPost()`, un método de Laminas que atrapa `application/x-www-form-urlencoded` mas no payloads de Axios en JSON sin ayuda de un strategy; o hay un pequeño error en la ejecución del Query (ej: boolean false = '' = 0).

### 2.4 Faltantes Operativos y de UI/UX

- **Ausencia de Función "Eliminar"**: La interfaz administrativa no proporciona método alguno en la GUI para suprimir u "ocultar lógicamente" lotes o QRs de la base de datos de manera explícita (sólo "Cambiar de estado").
- **Modal de Edición**: El botón principal de "Guardar Cambios" puede ocultarse en pantallas pequeñas, requiriendo desplazamiento interior.

## 3. Conclusión

El código subyacente y la arquitectura de generación de QR están estables, además el PDF generation (TCPDF) es rápido. Las prioridades recaen en asegurar que los controladores acepten correctamente los payloads y ajusten los métodos de `Router` / `DataTables` para optimizar la experiencia del administrador.
