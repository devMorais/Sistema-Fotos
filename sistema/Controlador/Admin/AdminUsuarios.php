<?php

namespace sistema\Controlador\Admin;

use sistema\Modelo\CategoriaModelo;
use sistema\Nucleo\Helpers;
use sistema\Modelo\UsuarioModelo;

/**
 * Classe Controladora - AdminUsuarios
 *
 * Esta classe é responsável por gerenciar as operações administrativas relacionadas aos usuarios.
 * Ela estende a classe AdminControlador.
 *
 * @package sistema\Controlador\Admin
 * @author Fernando
 */
class AdminUsuarios extends AdminControlador
{

    /**
     * Lista todas os usuarios no painel de administração.
     */
    public function listar(): void
    {
        $usuario = new UsuarioModelo();
        
        echo $this->template->renderizar('usuarios/listar.html', [
            'usuarios' => $usuario->busca()->resultado(true),
            'usuario' => [
                'total' => $usuario->total(),
                'ativo' => $usuario->busca('status = 1')->total(),
                'inativo' => $usuario->busca('status = 0')->total(),
            ]
        ]);
    }

    /**
     * Cadastra um usuario no painel de administração.
     */
    public function cadastrar()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)) {

            $usuario = new UsuarioModelo();

            $usuario->level = $dados['level'];
            $usuario->nome = $dados['nome'];
            $usuario->email  = $dados['email '];
            $usuario->senha = $dados['senha'];
            $usuario->status = $dados['status'];
            $usuario->cadastrado_em = date('Y-m-d H:i:s'); 


            if ($usuario->salvar()) {
                $this->mensagem->sucesso('Cadastro concluído com êxito.')->flash();

                Helpers::redirecionar('admin/usuarios/listar');
            }
        }

        echo $this->template->renderizar('usuarios/formulario.html', [
            'usuarios' => (new UsuarioModelo())->busca()
        ]);
    }

    /**
     * Edita uma categoria existente no painel de administração.
     *
     * @param int $id O ID da categoria a ser editada.
     */
    public function editar(int $id): void
    {
        $usuario = (new UsuarioModelo())->buscaPorId($id);

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($dados)) {

            $usuario = (new UsuarioModelo())->buscaPorId($id);

           
            $usuario->level = $dados['level'];
            $usuario->nome = $dados['nome'];
            $usuario->email  = $dados['email '];
            $usuario->senha = $dados['senha'];
            $usuario->status = $dados['status'];
            $usuario->atualizado_em = date('Y-m-d H:i:s'); 

            if ($usuario->salvar()) {
                $this->mensagem->sucesso('Atualização concluída com êxito.')->flash();

                Helpers::redirecionar('admin/usuarios/listar');
            }
        }

        echo $this->template->renderizar('usuarios/formulario.html', [
            'categoria' => $usuario
        ]);
    }

    /**
     * Deleta uma categoria no painel de administração.
     *
     * @param int $id O ID da categoria a ser deletada.
     */
    public function deletar(int $id): void
    {
        if (is_int($id)) {
            $usuario = (new UsuarioModelo())->buscaPorId($id);
            if (!$usuario) {
                $this->mensagem->erro('A categoria que você está tentando deletar não existe.')->flash();
                Helpers::redirecionar('admin/usuarios/listar');
            } else {
                if ($usuario->apagar("id = {$id}")) {
                    $this->mensagem->sucesso('Operação de exclusão concluída com êxito.')->flash();
                    Helpers::redirecionar('admin/usuarios/listar');
                } else {
                    $this->mensagem->erro($usuario->erro())->flash();
                    Helpers::redirecionar('admin/usuarios/listar');
                }
            }
        }
    }
}
