<?php

namespace sistema\Controlador\Admin;

use sistema\Modelo\CategoriaModelo;
use sistema\Nucleo\Helpers;

/**
 * Classe Controlodora - AdminCategorias
 *
 * @author Fernando
 */
class AdminCategorias extends AdminControlador
{

    public function listar(): void
    {
        $categoria = new CategoriaModelo();
        echo $this->template->renderizar('categorias/listar.html', [
            'categorias' => $categoria->busca()->ordem('status ASC, titulo ASC')->resultado(true),
            'total' => [
                'total' => $categoria->total(),
                'ativo' => $categoria->total('status = 1'),
                'inativo' => $categoria->total('status = 0')
            ]
        ]);
    }

    public function cadastrar()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)) {

            $categoria = new CategoriaModelo();

            $categoria->titulo = $dados['titulo'];
            $categoria->categoria_id = $dados['categoria_id'];
            $categoria->texto = $dados['texto'];
            $categoria->status = $dados['status'];

            if ($categoria->salvar()) {
                $this->mensagem->sucesso('Cadastro concluído com êxito.')->flash();

                Helpers::redirecionar('admin/categorias/listar');
            }
        }

        echo $this->template->renderizar('categorias/formulario.html', [
            'categorias' => (new CategoriaModelo())->busca()
        ]);
    }

    public function editar(int $id): void
    {
        $categoria = (new CategoriaModelo())->buscaPorId($id);

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($dados)) {

            $categoria = (new CategoriaModelo())->buscaPorId($id);

            $categoria->titulo = $dados['titulo'];
            $categoria->categoria_id = $dados['categoria_id'];
            $categoria->texto = $dados['texto'];
            $categoria->status = $dados['status'];

            if ($categoria->salvar()) {
                $this->mensagem->sucesso('Atualização concluída com êxito.')->flash();

                Helpers::redirecionar('admin/categorias/listar');
            }
        }

        echo $this->template->renderizar('categorias/formulario.html', [
            'categoria' => $categoria
           
        ]);
    }

    public function deletar(int $id): void
    {
        if (is_int($id)) {
            $categoria = (new CategoriaModelo())->buscaPorId($id);
            if (!$categoria) {
                $this->mensagem->erro('O produto que você está tentando deletar não existe.')->flash();
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
}
