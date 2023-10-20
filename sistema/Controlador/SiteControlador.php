<?php

namespace sistema\Controlador;

use sistema\Nucleo\Controlador;
use sistema\Modelo\ProdutoModelo;
use sistema\Nucleo\Helpers;
use sistema\Modelo\CategoriaModelo;
use sistema\Biblioteca\Paginar;

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
     * Categorias
     * @return array|null
     */
    public function categorias(): ?array
    {
        return (new CategoriaModelo())->busca("status = 1")->resultado(true);
    }

    /**
     * Método responsável por carregar os dados na pagina inicial
     * @return void
     */
    public function index(): void
    {
        $produtos = (new ProdutoModelo())->busca("status = 1");

        echo $this->template->renderizar('index.html', [
            'produtos' => [
                'slides' => $produtos->ordem('id DESC')->limite(3)->resultado(true),
                'produtos' => $produtos->ordem('id DESC')->limite(4)->offset(3)->resultado(true),
                'maisLidos' => (new ProdutoModelo())->busca("status = 1")->ordem('visitas DESC')->limite(5)->resultado(true),
            ],
            'categorias' => $this->categorias(),
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
    public function produtoPorId(string $categoria, string $slug): void
    {
        $produto = (new ProdutoModelo())->buscaPorSlug($slug);
        if (!$produto) {
            Helpers::redirecionar('404');
        }
        $produto->salvarVisitas();

        echo $this->template->renderizar('produto.html', [
            'produto' => $produto,
            'categorias' => $this->categorias(),
        ]);
    }

    /**
     *  Método responsável por fazer uma busca do produto através da categoria.
     * @param int $id
     * @return void
     */
    public function categoriaPorId(string $slug, int $pagina = null): void
    {
        $categoria = (new CategoriaModelo())->buscaPorSlug($slug);

        if (!$categoria) {
            Helpers::redirecionar('404');
        }
        $categoria->salvarVisitas();

        $produtos = (new ProdutoModelo());
        $total = $produtos->busca('categoria_id = :c AND status = :s', "c={$categoria->id}&s=1 COUNT(id)", 'id')->total();

        $paginar = new Paginar(Helpers::url('categoria/' . $slug), ($pagina ?? 1), 20, 3, $total);

        echo $this->template->renderizar('categoria.html', [
            'produtos' => $produtos->busca("categoria_id = {$categoria->id} AND status = 1")->limite($paginar->limite())->offset($paginar->offset())->resultado(true),
            'colecao' => $categoria,
            'paginacao' => $paginar->renderizar(),
            'paginacaoInfo' => $paginar->info(),
            'categorias' => $this->categorias(),
        ]);
    }

    /**
     * Sobre
     * @return void
     */
    public function sobre(): void
    {
        echo $this->template->renderizar('sobre.html', [
            'titulo' => 'Sobre nós',
            'categorias' => $this->categorias(),
        ]);
    }

    /**
     * ERRO 404
     * @return void
     */
    public function erro404(): void
    {
        echo $this->template->renderizar('404.html', [
            'titulo' => 'Página não encontrada',
            'categorias' => $this->categorias(),
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
