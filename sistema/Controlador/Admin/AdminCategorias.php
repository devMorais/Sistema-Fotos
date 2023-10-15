<?php

namespace sistema\Controlador\Admin;

use sistema\Modelo\CategoriaModelo;
use sistema\Nucleo\Helpers;

/**
 * Classe Controladora - AdminCategorias
 *
 * Esta classe é responsável por gerenciar as operações administrativas relacionadas a categorias.
 * Ela estende a classe AdminControlador.
 *
 * @package sistema\Controlador\Admin
 * @author Fernando
 */
class AdminCategorias extends AdminControlador
{

    /**
     * Lista todas as categorias no painel de administração.
     */
    public function listar(): void
    {
        $categoria = new CategoriaModelo();
        echo $this->template->renderizar('categorias/listar.html', [
            'categorias' => $categoria->busca()->ordem('status ASC, titulo ASC')->resultado(true),
            'total' => [
                'total' => $categoria->total(),
                'ativo' => $categoria->busca('status = 1')->total(),
                'inativo' => $categoria->busca('status = 0')->total(),
            ]
        ]);
    }

    /**
     * Cadastra uma nova categoria no painel de administração.
     */
    public function cadastrar()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)) {
            if ($this->validarDados($dados)) {
                $categoria = new CategoriaModelo();

                $categoria->titulo = $dados['titulo'];

                $categoria->texto = $dados['texto'];
                $categoria->status = $dados['status'];
                $categoria->cadastrado_em = date('Y-m-d H:i:s');

                if ($categoria->salvar()) {
                    $this->mensagem->sucesso('Cadastro concluído com êxito.')->flash();

                    Helpers::redirecionar('admin/categorias/listar');
                }
            }
        }

        echo $this->template->renderizar('categorias/formulario.html', [
            'categorias' => (new CategoriaModelo())->busca()
        ]);
    }

    /**
     * Edita uma categoria existente no painel de administração.
     *
     * @param int $id O ID da categoria a ser editada.
     */
    public function editar(int $id): void
    {
        $categoria = (new CategoriaModelo())->buscaPorId($id);

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($dados)) {

            $categoria = (new CategoriaModelo())->buscaPorId($id);

            $categoria->titulo = $dados['titulo'];

            $categoria->texto = $dados['texto'];
            $categoria->status = $dados['status'];
            $categoria->atualizado_em = date('Y-m-d H:i:s');

            if ($categoria->salvar()) {
                $this->mensagem->sucesso('Atualização concluída com êxito.')->flash();

                Helpers::redirecionar('admin/categorias/listar');
            }
        }

        echo $this->template->renderizar('categorias/formulario.html', [
            'categoria' => $categoria
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
            $categoria = (new CategoriaModelo())->buscaPorId($id);
            if (!$categoria) {
                $this->mensagem->erro('A categoria que você está tentando deletar não existe.')->flash();
                Helpers::redirecionar('admin/categorias/listar');
            } elseif ($categoria->produtos($categoria->id)) {

                $this->mensagem->erro("Antes de excluir a categoria {$categoria->titulo}, por favor, revise e atualize os produtos associados a ela, pois a categoria ainda contém produtos registrados.")->flash();
                Helpers::redirecionar('admin/categorias/listar');
            } else {
                if ($categoria->apagar("id = {$id}")) {
                    $this->mensagem->sucesso('Operação de exclusão concluída com êxito.')->flash();
                    Helpers::redirecionar('admin/categorias/listar');
                } else {
                    $this->mensagem->erro($categoria->erro())->flash();
                    Helpers::redirecionar('admin/categorias/listar');
                }
            }
        }
    }

    /**
     * Checa os dados do formulário
     * @param array $dados
     * @return bool
     */
    public function validarDados(array $dados): bool
    {
        if (empty($dados['titulo'])) {
            $this->mensagem->alerta('Informe um titulo para categoria')->flash();
            return false;
        }
        if (empty($dados['texto'])) {
            $this->mensagem->alerta('Informe a descrição da categoria')->flash();
            return false;
        }


        return true;
    }
}
