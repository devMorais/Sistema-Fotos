<?php

namespace sistema\Modelo;

use sistema\Nucleo\Modelo;
use sistema\Nucleo\Sessao;
use sistema\Nucleo\Helpers;

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

        if (!Helpers::verificarSenha($dados['senha'], $usuario->senha)) {
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

        $usuario->ultimo_login = date('Y-m-d H:i:s');
        $usuario->salvar();

        (new Sessao())->criar('usuarioId', $usuario->id);

        $this->mensagem->sucesso("{$usuario->nome}, seja bem vindo(a) ao painel de controle!")->flash();
        return true;
    }

    /**
     * Salva os dados do modelo no banco de dados.
     *
     * Este método decide se deve cadastrar um novo registro ou atualizar um existente com base no ID do modelo.
     * Em seguida, atualiza os dados do modelo com os dados recém-salvos do banco de dados.
     *
     * @return bool true se a operação for bem-sucedida, false em caso de erro.
     */
    public function salvar(): bool
    {

        if ($this->busca("email = :e AND id != :id", "e={$this->email}&id={$this->id}")->resultado()) {
            $this->mensagem->alerta("O E-mail " . $this->dados->email . " já está cadastrado");
            return false;
        }

        parent::salvar();

        return true;
    }
}
