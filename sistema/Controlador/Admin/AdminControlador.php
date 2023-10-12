<?php

namespace sistema\Controlador\Admin;

use sistema\Nucleo\Controlador;
use sistema\Nucleo\Helpers;

/**
 * Classe  AdminControlador
 *
 * @author Fernando
 */
class AdminControlador extends Controlador
{

    public function __construct()
    {
        parent::__construct('templates/admin/views');

        $usuario = false;

        if (!$usuario) {
            $this->mensagem->erro('Acesso permitido somente aos admnistradores do sistema, faça login! ')->flash();
            Helpers::redirecionar('admin/login');
        }
    }
}
