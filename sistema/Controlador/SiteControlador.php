<?php

namespace sistema\Controlador;

use sistema\Nucleo\Controlador;
use sistema\Modelo\ProdutoModelo;
use sistema\Nucleo\Helpers;
use sistema\Modelo\CategoriaModelo;

class SiteControlador extends Controlador
{

    /**
     * Método construtor iniciado quando a classe é instanciada.
     */
    public function __construct()
    {
        parent::__construct('templates/site/views');
    }

    /**
     * Retorna uma lista das categorias
     * @return array
     */
    public function categorias(): array
    {
        return (new CategoriaModelo())->busca()->resultado(true);
    }

    /**
     * Método responsável por carregar os dados na pagina inicial
     * @return void
     */
    public function index(): void
    {
        $produtos = (new ProdutoModelo())->busca("status = 1")->resultado(true);

        echo $this->template->renderizar('index.html', [
            'produtos' => $produtos,
            'categorias' => $this->categorias()
        ]);
    }

    /**
     * Método responsável por fazer uma busca em tempo real dos produtos por titulo.
     * @return void
     */
    public function buscar(): void
    {
        $busca = filter_input(INPUT_POST, 'busca', FILTER_DEFAULT);
        if (isset($busca)) {
            $produtos = (new ProdutoModelo())->busca("status = 1 AND titulo LIKE '%{$busca}%'")->resultado(true);
            foreach ($produtos as $produto) {
                echo "<li class='list-group-item'><a href=" . Helpers::url('produto/') . $produto->id . ">$produto->titulo</a></li>";
            }
        }
    }

    /**
     * Método responsável por fazer uma busca do produto através do ID.
     * @param int $id
     * @return void
     */
    public function produtoPorId(int $id): void
    {
        $produto = (new ProdutoModelo())->buscaPorId($id);

        if (!$produto) {
            Helpers::redirecionar('404');
        }

        echo $this->template->renderizar('produto.html', [
            'produto' => $produto,
            'categorias' => $this->categorias()
        ]);
    }

    /**
     *  Método responsável por fazer uma busca do produto através da categoria.
     * @param int $id
     * @return void
     */
    public function categoriaPorId(int $id): void
    {
        $produtos = (new CategoriaModelo())->buscaPorId($id);

        echo $this->template->renderizar('categoria.html', [
            'produtos' => $produtos,
            'categorias' => $this->categorias()
        ]);
    }

    /**
     * Método responsável por carregar os dados na pagina sobre
     * @return void
     */
    public function sobre(): void
    {
        echo $this->template->renderizar('sobre.html', [
            'titulo' => 'Sobre nós',
            'categorias' => $this->categorias()
        ]);
    }

    /**
     * Método responsável por carregar os dados na pagina erro
     * @return void
     */
    public function erro404(): void
    {
        echo $this->template->renderizar('404.html', [
            'titulo' => 'Página não encontrada',
            'categorias' => $this->categorias()
        ]);
    }

    /**
     * Método responsável por carregar os dados na pagina contato.
     */
    public function contato()
    {
        echo $this->template->renderizar('contato.html', [
        ]);
    }
}
