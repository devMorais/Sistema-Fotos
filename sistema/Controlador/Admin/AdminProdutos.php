<?php

namespace sistema\Controlador\Admin;

use sistema\Modelo\ProdutoModelo;
use sistema\Modelo\CategoriaModelo;
use sistema\Nucleo\Helpers;

/**
 * Classe - Dashboard do painel administrativo.
 *
 * @author Fernando
 */
class AdminProdutos extends AdminControlador
{

    public function listar(): void
    {
        $produto = new ProdutoModelo();
        echo $this->template->renderizar('produtos/listar.html', [
            'produtos' => $produto->busca()->ordem('status ASC, id DESC')->resultado(true),
            'total' => [
                'total' => $produto->total(),
                'ativo' => $produto->total('status = 1'),
                'inativo' => $produto->total('status = 0')
            ]
        ]);
    }

    public function cadastrar()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)) {
            (new ProdutoModelo())->armazenar($dados);
            $this->mensagem->sucesso('Cadastro concluído com êxito.')->flash();

            Helpers::redirecionar('admin/produtos/listar');
        }

        echo $this->template->renderizar('produtos/formulario.html', [
            'categorias' => (new CategoriaModelo())->busca()
        ]);
    }

    public function editar(int $id): void
    {
        $produto = (new ProdutoModelo())->buscaPorId($id);

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($dados)) {
            (new ProdutoModelo())->atualizar($dados, $id);
            $this->mensagem->sucesso('Edição realizada com êxito.')->flash();
            Helpers::redirecionar('admin/produtos/listar');
        }

        echo $this->template->renderizar('produtos/formulario.html', [
            'produto' => $produto,
            'categorias' => (new CategoriaModelo())->busca()
        ]);
    }

    public function deletar(int $id): void
    {
        (new ProdutoModelo())->deletar($id);
        $this->mensagem->sucesso('Operação de exclusão concluída com êxito.')->flash();
        Helpers::redirecionar('admin/produtos/listar');
    }
}
