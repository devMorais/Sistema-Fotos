<?php

namespace sistema\Controlador\Admin;

use sistema\Nucleo\Controlador;
use sistema\Nucleo\Helpers;
use sistema\Modelo\UsuarioModelo;

/**
 * Classe AdminLogin
 *
 * Esta classe lida com as operações relacionadas ao login no painel de administração.
 * Ela estende a classe Controlador e define um método para processar o login do administrador.
 *
 * @package sistema\Controlador\Admin
 * @author Fernando
 */
class AdminLogin extends Controlador
{

    /**
     * Construtor da classe AdminLogin.
     */
    public function __construct()
    {
        parent::__construct('templates/admin/views');
    }

    /**
     * Método para processar o login do administrador.
     */
    public function login(): void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($dados)) {
            if ($this->checarDadosLogin($dados)) {

                $usuario = (new UsuarioModelo())->login($dados, 3);
                if ($usuario) {
                    Helpers::redirecionar('admin/login');
                }
            }
        }

        echo $this->template->renderizar('login.html', []);
    }

    /**
     * Método privado para verificar os dados de login.
     *
     * @param array $dados Os dados de login.
     * @return bool true se os dados de login forem válidos, false caso contrário.
     */
    private function checarDadosLogin(array $dados): bool
    {

        if (empty($dados['email'])) {
            $this->mensagem->alerta('Todos os campos são obrigatórios.')->flash();
            return false;
        }
        if (empty($dados['senha'])) {
            $this->mensagem->alerta('Todos os campos são obrigatórios.')->flash();
            return false;
        }
        return true;
    }
}
