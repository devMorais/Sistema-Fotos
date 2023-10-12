<?php

namespace sistema\Modelo;

use sistema\Nucleo\Modelo;
use sistema\Nucleo\Sessao;

/**
 * Classe UsuarioModelo
 *
 * Esta classe representa o modelo de usuário e lida com operações relacionadas a usuários no sistema.
 *
 * @package sistema\Modelo
 * @author Fernando
 */
class UsuarioModelo extends Modelo
{

    /**
     * Construtor da classe UsuarioModelo.
     */
    public function __construct()
    {
        parent::__construct('usuarios');
    }

    /**
     * Busca um usuário por email.
     *
     * @param string $email O email do usuário a ser buscado.
     * @return UsuarioModelo|null O usuário encontrado ou null em caso de erro.
     */
    public function buscaPorEmail(string $email): ?UsuarioModelo
    {
        $busca = $this->busca("email = :e", "e={$email}");
        return $busca->resultado();
    }

    /**
     * Realiza o login do usuário.
     *
     * @param array $dados Os dados de login, incluindo email e senha.
     * @param int $level O nível mínimo de acesso necessário para efetuar o login.
     * @return bool true se o login for bem-sucedido, false caso contrário.
     */
    public function login(array $dados, int $level = 1)
    {
        $usuario = (new UsuarioModelo())->buscaPorEmail($dados['email']);

        if (!$usuario) {
            $this->mensagem->alerta("Dados incorretos.")->flash();
            return false;
        }

        if ($dados['senha'] != $usuario->senha) {
            $this->mensagem->alerta("Dados incorretos.")->flash();
            return false;
        }

        if ($usuario->status != 1) {
            $this->mensagem->alerta("Dados incorretos.")->flash();
            return false;
        }

        if ($usuario->level < $level) {
            $this->mensagem->alerta("Dados incorretos.")->flash();
            return false;
        }

        (new Sessao())->criar('usuarioId', $usuario->id);

        $this->mensagem->sucesso("{$usuario->nome}, seja bem vindo(a) ao painel de controle!")->flash();
        return true;
    }
}
