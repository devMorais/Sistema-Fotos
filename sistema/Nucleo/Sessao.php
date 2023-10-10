<?php

namespace sistema\Nucleo;

/**
 * Classe para gerenciar sessões de usuário.
 *
 * @author Fernando
 */
class Sessao
{

    /**
     * Inicializa a sessão se ainda não estiver iniciada.
     */
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
    }

    /**
     * Cria ou atualiza uma variável de sessão.
     *
     * @param string $chave A chave da variável de sessão.
     * @param mixed $valor O valor a ser armazenado na variável de sessão.
     *
     * @return Sessao O próprio objeto Sessao para permitir encadeamento de métodos.
     */
    public function criar(string $chave, mixed $valor): Sessao
    {
        $_SESSION[$chave] = (is_array($valor) ? (object) $valor : $valor);
        return $this;
    }

    /**
     * Carrega todas as variáveis de sessão como um objeto.
     *
     * @return object|null Um objeto contendo todas as variáveis de sessão ou NULL se não houver sessão iniciada.
     */
    public function carregar(): ?object
    {
        return (object) $_SESSION;
    }

    /**
     * Verifica se uma variável de sessão com a chave fornecida existe.
     *
     * @param string $chave A chave da variável de sessão a ser verificada.
     *
     * @return bool True se a variável de sessão existir, false caso contrário.
     */
    public function checar(string $chave): bool
    {
        return isset($_SESSION[$chave]);
    }

    /**
     * Remove uma variável de sessão com a chave fornecida.
     *
     * @param string $chave A chave da variável de sessão a ser removida.
     *
     * @return Sessao O próprio objeto Sessao para permitir encadeamento de métodos.
     */
    public function limpar(string $chave): Sessao
    {
        unset($_SESSION[$chave]);
        return $this;
    }

    /**
     * Destrói a sessão atual.
     *
     * @return Sessao O próprio objeto Sessao para permitir encadeamento de métodos.
     */
    public function deletar(): Sessao
    {
        session_destroy();
        return $this;
    }
}
