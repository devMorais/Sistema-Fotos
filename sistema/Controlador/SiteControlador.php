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
        return (new CategoriaModelo())->busca("status = 1 ")->resultado(true);
    }

    /**
     * Método responsável por carregar os dados na pagina inicial
     * @return void
     */
    public function index(): void
    {
        $produtos = (new ProdutoModelo())->busca("status = 1");

        echo $this->template->renderizar('index.html', [
            'produtos' => $produtos->resultado(true),
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
            if ($produtos) {
                foreach ($produtos as $produto) {
                    echo "<li class='list-group-item'><a href=" . Helpers::url('produto/') . $produto->slug . ">$produto->titulo</a></li>";
                }
            }
        }
    }

    /**
     * Método responsável por fazer uma busca do produto através de slug.
     * @param string $slug
     * @return void
     */
    public function produtoPorId(string $slug): void
    {
        $produto = (new ProdutoModelo())->buscaPorSlug($slug);

        if (!$produto) {
            Helpers::redirecionar('404');
        }


        $produto->salvarVisitas();

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
    public function categoriaPorId(string $slug): void
    {
        $categoria = (new CategoriaModelo())->buscaPorSlug($slug);

        if (!$categoria) {
            Helpers::redirecionar('404');
        }


        $categoria->salvarVisitas();

        echo $this->template->renderizar('categoria.html', [
            'produtos' => (new CategoriaModelo())->produtos($categoria->id),
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
