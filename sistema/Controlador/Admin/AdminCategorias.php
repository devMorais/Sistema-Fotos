<?php

namespace sistema\Controlador\Admin;

use sistema\Modelo\CategoriaModelo;
use sistema\Nucleo\Helpers;

/**
 * Classe - Dashboard do painel administrativo.
 *
 * @author Fernando
 */
class AdminCategorias extends AdminControlador
{

    public function listar(): void
    {
        $categoria = new CategoriaModelo();
        echo $this->template->renderizar('categorias/listar.html', [
            'categorias' => $categoria->busca(),
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
            (new CategoriaModelo())->armazenar($dados);
            $this->mensagem->sucesso('Cadastro concluído com êxito.')->flash();
            Helpers::redirecionar('admin/categorias/listar');
        }

        echo $this->template->renderizar('categorias/formulario.html', []);
    }

    public function editar(int $id): void
    {
        $categoria = (new CategoriaModelo())->buscaPorId($id);

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)) {
            (new CategoriaModelo())->atualizar($dados, $id);
            $this->mensagem->sucesso('Edição realizada com êxito.')->flash();

            Helpers::redirecionar('admin/categorias/listar');
        }

        echo $this->template->renderizar('categorias/formulario.html', [
            'categoria' => $categoria
        ]);
    }

    public function deletar(int $id): void
    {
        (new CategoriaModelo())->deletar($id);
        $this->mensagem->sucesso('Operação de exclusão concluída com êxito.')->flash();

        Helpers::redirecionar('admin/categorias/listar');
    }
}
