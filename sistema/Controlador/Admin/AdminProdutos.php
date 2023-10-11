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
                'ativo' => $produto->busca('status = 1')->total(),
                'inativo' => $produto->busca('status = 0')->total()
            ]
        ]);
    }

    public function cadastrar()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)) {

            $produto = new ProdutoModelo();

            $produto->titulo = $dados['titulo'];
            $produto->categoria_id = $dados['categoria_id'];
            $produto->texto = $dados['texto'];
            $produto->status = $dados['status'];

            if ($produto->salvar()) {
                $this->mensagem->sucesso('Cadastro concluído com êxito.')->flash();

                Helpers::redirecionar('admin/produtos/listar');
            }
        }

        echo $this->template->renderizar('produtos/formulario.html', [
            'categorias' => (new CategoriaModelo())->busca()->resultado(true)
        ]);
    }

    public function editar(int $id): void
    {
        $produto = (new ProdutoModelo())->buscaPorId($id);

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($dados)) {

            $produto = (new ProdutoModelo())->buscaPorId($id);

            $produto->titulo = $dados['titulo'];
            $produto->categoria_id = $dados['categoria_id'];
            $produto->texto = $dados['texto'];
            $produto->status = $dados['status'];

            if ($produto->salvar()) {
                $this->mensagem->sucesso('Atualização concluída com êxito.')->flash();

                Helpers::redirecionar('admin/produtos/listar');
            }
        }

        echo $this->template->renderizar('produtos/formulario.html', [
            'produto' => $produto,
            'categorias' => (new CategoriaModelo())->busca()->resultado(true)
        ]);
    }

    public function deletar(int $id): void
    {
        if (is_int($id)) {
            $produto = (new ProdutoModelo())->buscaPorId($id);
            if (!$produto) {
                $this->mensagem->erro('O produto que você está tentando deletar não existe.')->flash();
                Helpers::redirecionar('admin/produtos/listar');
            } else {
                if ($produto->deletar()) {
                    $this->mensagem->sucesso('Operação de exclusão concluída com êxito.')->flash();
                    Helpers::redirecionar('admin/produtos/listar');
                } else {
                    $this->mensagem->erro($produto->erro())->flash();
                    Helpers::redirecionar('admin/produtos/listar');
                }
            }
        }
    }
}
