# Reporte de Revisión y Diagnóstico: WSOD en `/vehiculos/admin`

Este documento resume las pruebas realizadas en el entorno local, las validaciones del código fuente y las hipótesis sobre la causa del "White Screen of Death" (WSOD) que ocurre exclusivamente en producción (Cloudflare + VPS) en la ruta `/vehiculos/admin` (y derivadas) tras renombrar el módulo `VehiculosQr` a `Vehiculos`.

## 1. Lo que se ha comprobado (Funciona correctamente)

### ✅ Nombres de Archivos y Carpetas en GIT (Case Sensitivity)
El sistema local utiliza Windows (no distingue mayúsculas/minúsculas), pero el VPS con Linux sí. Se verificó utilizando `git ls-tree` que las carpetas fueron integradas a git con los nombres y las capitalizaciones exactas:
*   `module/Vehiculos/` (Módulo)
*   `module/Vehiculos/src/Controller/AdminController.php` (Código fuente)
*   `module/Vehiculos/view/vehiculos/admin/gestion.phtml` (Vistas)
No existen discrepancias de case sensitivity rastreadas por git.

### ✅ Rutas y Configuración del View Manager (module.config.php)
Todas las rutas referenciadas en el `layout_vehiculos.phtml` (ej: `vehiculos-admin-generar-lote`, `vehiculos-admin-usuarios`, `vehiculos-editar`) están **perfectamente definidas** en `module.config.php`. No hay rutas faltantes que pudieran provocar una `RuntimeException` irrecuperable en la vista.
El `template_path_stack` está configurado para buscar en `module/Vehiculos/view`, el cual coincide con el estándar.

### ✅ Autoloading y Composer
En `composer.json`, el PSR-4 fue actualizado correctamente (`"Vehiculos\\": "module/Vehiculos/src/"`).

### ✅ Control de ESPACIOS en Blanco y BOM
Se implementó un script (`test-bom.php`) para comprobar si al momento de hacer *Reemplazar todo* se había introducido accidentalmente un caracter "BOM" (Byte Order Mark), un espacio en blanco inicial (` <?php`) o un salto de línea final antes de enviar las cabeceras REST, lo cual "ahogaría" el redireccionamiento de PHP provocando pantalla blanca. **Resultado:** Ningún archivo del módulo tiene espacios parásitos.

---

## 2. Anomalía Cazada (Síntoma Clave en Producción)
Cuando enviamos una petición a `https://aprotec.cl/vehiculos/admin` sin loguearnos usando `curl`:
1.  **Localmente:** Laminas intercepta el evento en la función `AdminController::onDispatch()`, verifica que no tengamos sesión y retorna un redireccionamiento HTTP `302 Found` con `Location: /vehiculos/login`.
2.  **En Producción:** Cloudflare/Nginx responden un **HTTP 200 OK** con tamaño exacto de **1 byte (o vacío)**. NO lanza el 302, pero tampoco lanza un `500 Internal Server Error`. 

La página de `https://aprotec.cl/vehiculos/login` en producción sí funciona y devuelve el HTML completo (tamaño 12.5 KB).

---

## 3. Sospechas del Error (Hipótesis)

### Hipótesis A: Cache de la Inyección de Dependencias (ServiceManager / Config)
A pesar de recompilar la app en Dokploy, Laminas podría estar leyendo una caché en el volumen persistente dentro de la ruta `data/cache/*.php`. Si la caché de `modulemap` o configuración persiste, busca instancias con inyección a la base de datos que están usando el namespace antiguo, el cual revienta silenciosamente al invocar el Factory.

### Hipótesis B: Excepción Crítica Silenciada 
Si al entrar a `AdminController` el servicio `AuthService` o `QrService` (inyectados por constructor) arroja un error crítico de conexión a Base de Datos (o similar específico del VPS), Laminas intenta manejar el error retornando un `500` mediante su plantilla `error/index`.
Si por la reestructuración la ruta al layout o a la vista de error está perdida o corrompida, el `ErrorHandler` de Laminas en sí entra en error ("Excepción durante el manejo de excepción") y el proceso PHP muere abruptamente retornando `200 OK` sin buffer de respuesta (comportamiento muy común en PHP-FPM bajo fallas cataclísmicas en frameworks completos sin `display_errors`).

### Hipótesis C: Bloqueo de Cloudflare a nivel ruta u objeto
Aunque menos probable dado que devuelve 1 byte y Status 200 dinámico, hay que considerar una regla page-rule antigua en Cloudflare. 

---

## 4. Próxima Acción (Recomendación)

Dado que en código fuente el módulo está íntegro y estructuralmente sano, necesitamos obligar a Producción a "gritar" el error en lugar de quedarse callado. 

Por favor, realiza lo siguiente en tu entorno local antes de hacer despliegue:

1. **Borrar manualmente cualquier caché de Laminas:** Verifica si tienes el directorio `data/cache/` (no ignorado en .git) y límpialo.
2. **Habilitaremos un Error_Log estricto en el Index:** Te indicaré cómo modificar `public/index.php` para que ante cualquier error fatal o excepción "swallowed" se guarde el rastro en un archivo `.log` persistente o se muestre en pantalla temporalmente mientras hacemos la prueba. 

Si estás de acuerdo con esto, procederé a realizar los commits.
