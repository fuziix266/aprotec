<?php

namespace Vehiculos\Service;

use Vehiculos\Repository\QrUsuariosRepository;
use Laminas\Session\Container as SessionContainer;

class AuthService
{
    private QrUsuariosRepository $usuariosRepo;
    private SessionContainer $session;

    public function __construct(QrUsuariosRepository $usuariosRepo)
    {
        $this->usuariosRepo = $usuariosRepo;
        $this->session = new SessionContainer('vehiculos_qr_auth');
    }

    /**
     * Intentar login
     */
    public function login(string $correo, string $password): array
    {
        // Validar dominio — el sistema es para la Municipalidad de Arica
        if (!str_ends_with($correo, '@municipalidadarica.cl')) {
            return ['success' => false, 'error' => 'Solo se permiten correos @municipalidadarica.cl'];
        }

        $usuario = $this->usuariosRepo->findByCorreo($correo);

        if (!$usuario) {
            return ['success' => false, 'error' => 'Credenciales incorrectas'];
        }

        if ($usuario['activo'] != 1) {
            return ['success' => false, 'error' => 'Usuario inactivo. Contacte al administrador.'];
        }

        if (!password_verify($password, $usuario['password_hash'])) {
            return ['success' => false, 'error' => 'Credenciales incorrectas'];
        }

        // Guardar en sesión
        $this->session->user_id = $usuario['id'];
        $this->session->nombre  = $usuario['nombre'];
        $this->session->correo  = $usuario['correo'];
        $this->session->rol     = $usuario['rol'];

        return [
            'success' => true,
            'usuario' => [
                'id'     => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'correo' => $usuario['correo'],
                'rol'    => $usuario['rol'],
            ]
        ];
    }

    /**
     * Cerrar sesión
     */
    public function logout(): void
    {
        $this->session->getManager()->destroy();
    }

    /**
     * Verificar si está autenticado
     */
    public function isAuthenticated(): bool
    {
        return isset($this->session->user_id);
    }

    /**
     * Obtener usuario actual
     */
    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'id' => $this->session->user_id,
            'nombre' => $this->session->nombre,
            'correo' => $this->session->correo,
            'rol' => $this->session->rol,
        ];
    }

    /**
     * Verificar si es admin
     */
    public function isAdmin(): bool
    {
        return $this->isAuthenticated() && $this->session->rol === 'ADMIN';
    }

    /**
     * Verificar si es inspector
     */
    public function isInspector(): bool
    {
        return $this->isAuthenticated() && $this->session->rol === 'INSPECTOR';
    }
}
