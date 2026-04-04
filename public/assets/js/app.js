/**
 * Sistema QR Vehículos Municipales - DIDECO Arica
 * JavaScript principal - Geolocalización y utilidades
 */

// ============================================
// CONFIGURACIÓN GLOBAL
// ============================================
const APP_CONFIG = {
    gpsTimeout: 10000, // 10 segundos
    gpsMaxAge: 0,
    gpsHighAccuracy: true,
};

// ============================================
// UTILIDADES
// ============================================
const Utils = {
    /**
     * Mostrar loading overlay
     */
    showLoading(message = 'Cargando...') {
        let overlay = document.querySelector('.loading-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="spinner-container">
                    <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 fs-5">${message}</p>
                </div>
            `;
            document.body.appendChild(overlay);
        }
        overlay.classList.add('active');
    },

    /**
     * Ocultar loading overlay
     */
    hideLoading() {
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) {
            overlay.classList.remove('active');
        }
    },

    /**
     * Mostrar alerta con SweetAlert2
     */
    showAlert(title, message, type = 'info') {
        Swal.fire({
            icon: type,
            title: title,
            text: message,
            confirmButtonColor: '#0d47a1',
        });
    },

    /**
     * Mostrar confirmación
     */
    async showConfirm(title, message) {
        const result = await Swal.fire({
            icon: 'question',
            title: title,
            text: message,
            showCancelButton: true,
            confirmButtonColor: '#0d47a1',
            cancelButtonColor: '#c62828',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
        });
        return result.isConfirmed;
    },

    /**
     * Mostrar toast
     */
    showToast(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        Toast.fire({
            icon: type,
            title: message,
        });
    },
};

// ============================================
// SERVICIO DE GEOLOCALIZACIÓN
// ============================================
const GeoService = {
    /**
     * Verificar si el navegador soporta geolocalización
     */
    isSupported() {
        return 'geolocation' in navigator;
    },

    /**
     * Obtener posición actual
     */
    async getCurrentPosition() {
        return new Promise((resolve, reject) => {
            if (!this.isSupported()) {
                reject(new Error('Tu navegador no soporta geolocalización'));
                return;
            }

            Utils.showLoading('Obteniendo ubicación GPS...');

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    Utils.hideLoading();
                    resolve({
                        lat: position.coords.latitude,
                        lon: position.coords.longitude,
                        accuracy: position.coords.accuracy,
                    });
                },
                (error) => {
                    Utils.hideLoading();
                    let mensaje = 'Error al obtener ubicación';
                    
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            mensaje = 'Debes permitir el acceso a tu ubicación para continuar';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            mensaje = 'No se pudo determinar tu ubicación. Verifica que el GPS esté activado';
                            break;
                        case error.TIMEOUT:
                            mensaje = 'Tiempo de espera agotado. Intenta nuevamente';
                            break;
                    }
                    
                    reject(new Error(mensaje));
                },
                {
                    enableHighAccuracy: APP_CONFIG.gpsHighAccuracy,
                    timeout: APP_CONFIG.gpsTimeout,
                    maximumAge: APP_CONFIG.gpsMaxAge,
                }
            );
        });
    },

    /**
     * Solicitar permiso y obtener ubicación
     */
    async requestLocationAndGet() {
        try {
            const position = await this.getCurrentPosition();
            return { success: true, position };
        } catch (error) {
            return { success: false, error: error.message };
        }
    },
};

// ============================================
// SERVICIO DE API
// ============================================
const ApiService = {
    /**
     * Hacer petición POST con JSON
     */
    async post(url, data = {}) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(data),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    /**
     * Hacer petición POST con FormData
     */
    async postForm(url, formData) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    /**
     * Hacer petición GET
     */
    async get(url) {
        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },
};

// ============================================
// VALIDADORES
// ============================================
const Validators = {
    /**
     * Validar correo electrónico
     */
    email(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    },

    /**
     * Validar correo municipal
     */
    emailMunicipal(email) {
        return email.endsWith('@municipalidadarica.cl');
    },

    /**
     * Validar RUT chileno
     */
    rut(rut) {
        // Remover puntos y guión
        rut = rut.replace(/\./g, '').replace(/-/g, '');
        
        if (rut.length < 2) return false;
        
        const cuerpo = rut.slice(0, -1);
        const dv = rut.slice(-1).toUpperCase();
        
        // Calcular dígito verificador
        let suma = 0;
        let multiplo = 2;
        
        for (let i = cuerpo.length - 1; i >= 0; i--) {
            suma += parseInt(cuerpo.charAt(i)) * multiplo;
            multiplo = multiplo === 7 ? 2 : multiplo + 1;
        }
        
        const dvEsperado = 11 - (suma % 11);
        const dvCalculado = dvEsperado === 11 ? '0' : dvEsperado === 10 ? 'K' : String(dvEsperado);
        
        return dv === dvCalculado;
    },

    /**
     * Formatear RUT chileno
     */
    formatRut(rut) {
        // Remover todo excepto números y K
        rut = rut.replace(/[^0-9Kk]/g, '').toUpperCase();
        
        if (rut.length <= 1) return rut;
        
        const cuerpo = rut.slice(0, -1);
        const dv = rut.slice(-1);
        
        // Formatear con puntos
        let cuerpoFormateado = '';
        let contador = 0;
        
        for (let i = cuerpo.length - 1; i >= 0; i--) {
            if (contador === 3) {
                cuerpoFormateado = '.' + cuerpoFormateado;
                contador = 0;
            }
            cuerpoFormateado = cuerpo.charAt(i) + cuerpoFormateado;
            contador++;
        }
        
        return cuerpoFormateado + '-' + dv;
    },

    /**
     * Validar patente chilena
     */
    patente(patente) {
        patente = patente.toUpperCase().replace(/[^A-Z0-9]/g, '');
        
        // Formato antiguo: AB1234 o ABC123
        // Formato nuevo: ABCD12
        const regexAntiguo = /^[A-Z]{2,3}[0-9]{3,4}$/;
        const regexNuevo = /^[A-Z]{4}[0-9]{2}$/;
        
        return regexAntiguo.test(patente) || regexNuevo.test(patente);
    },

    /**
     * Validar teléfono chileno
     */
    telefono(telefono) {
        // Remover espacios y caracteres especiales
        telefono = telefono.replace(/\s/g, '').replace(/[^0-9+]/g, '');
        
        // Debe tener 9 dígitos (celular) o 11-12 con código de país
        return telefono.length === 9 || telefono.length === 11 || telefono.length === 12;
    },
};

// ============================================
// EXPORTAR PARA USO GLOBAL
// ============================================
window.APP = {
    Utils,
    GeoService,
    ApiService,
    Validators,
};

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    console.log('Sistema QR Vehículos Municipales - Inicializado');
    
    // Auto-formatear inputs de RUT
    document.querySelectorAll('input[name="rut"]').forEach(input => {
        input.addEventListener('input', (e) => {
            e.target.value = Validators.formatRut(e.target.value);
        });
    });
    
    // Validar formularios de Bootstrap
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
