<?php

namespace sistema\Controlador\Admin;

use sistema\Modelo\ProdutoModelo;
use sistema\Modelo\CategoriaModelo;
use sistema\Nucleo\Helpers;
use Verot\Upload\Upload;

/**
 * Classe Controladora de Administração de Produtos
 *
 * Esta classe é responsável por lidar com operações administrativas relacionadas a produtos.
 * Ela estende a classe AdminControlador.
 *
 * @author Fernando
 */
class AdminProdutos extends AdminControlador
{

    private string $capa;

    /**
     * Método responsável por exibir os dados tabulados utilizando o plugin datatables
     * @return void
     */
    public function datatable(): void
    {

        $datatable = $_REQUEST;
        $datatable = filter_var_array($datatable, FILTER_SANITIZE_SPECIAL_CHARS);

        $limite = $datatable['length'];
        $offset = $datatable['start'];
        $busca = $datatable['search']['value'];

        $colunas = [
            0 => 'id',
            2 => 'titulo',
            3 => 'categoria_id',
            4 => 'visitas',
            5 => 'status'
        ];

        $ordem = " " . $colunas[$datatable['order'][0]['column']] . " ";
        $ordem .= " " . $datatable['order'][0]['dir'] . " ";

        $produtos = new ProdutoModelo();

        if (empty($busca)) {
            $produtos->busca()->ordem($ordem)->limite($limite)->offset($offset);
            $total = (new ProdutoModelo())->busca(null, 'COUNT(id)', 'id')->total();
        } else {
            $produtos->busca("id LIKE '%{$busca}%' OR titulo LIKE '%{$busca}%' ")->limite($limite)->offset($offset);
            $total = $produtos->total();
        }

        $dados = [];

        if ($produtos->resultado(true)) {
            foreach ($produtos->resultado(true) as $produto) {
                $dados[] = [
                    $produto->id,
                    $produto->capa,
                    $produto->titulo,
                    $produto->categoria()->titulo ?? '-----',
                    Helpers::formatarNumero($produto->visitas),
                    $produto->status
                ];
            }
        }


        $retorno = [
            "draw" => $datatable['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $dados
        ];

        echo json_encode($retorno);
    }

    /**
     * Lista produtos
     * @return void
     */
    public function listar(): void
    {
        $produtos = new ProdutoModelo();

        echo $this->template->renderizar('produtos/listar.html', [
            'total' => [
                'produtos' => 0,
                'produtosAtivo' => 0,
                'produtosInativo' => 0,
            ]
        ]);
    }

    /**
     * Cadastrar um novo produto no painel de administração.
     *
     * Este método lida com a criação de um novo produto. Ele processa os dados do formulário enviado,
     * cria um novo produto e o salva no banco de dados. Ele também trata mensagens de sucesso e falha.
     */
    public function cadastrar()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($dados)) {
            if ($this->validarDados($dados)) {
                $produto = new ProdutoModelo();

                $produto->usuario_id = $this->usuario->id;
                $produto->categoria_id = $dados['categoria_id'];
                $produto->slug = Helpers::slug($dados['titulo']);
                $produto->titulo = $dados['titulo'];
                $produto->texto = $dados['texto'];
                $produto->status = $dados['status'];
                $produto->capa = $this->capa ?? null;

                if ($produto->salvar()) {
                    $this->mensagem->sucesso('Cadastrado com sucesso.')->flash();

                    Helpers::redirecionar('admin/produtos/listar');
                } else {
                    $this->mensagem->erro($produto->erro())->flash();
                    Helpers::redirecionar('admin/produtos/listar');
                }
            }
        }

        echo $this->template->renderizar('produtos/formulario.html', [
            'categorias' => (new CategoriaModelo())->busca()->resultado(true),
            'produto' => $dados
        ]);
    }

    /**
     * Editar um produto no painel de administração.
     *
     * Este método permite a edição de um produto existente. Ele recupera os detalhes do produto,
     * processa os dados do formulário enviado, atualiza o produto e trata mensagens de sucesso e falha.
     *
     * @param int $id O ID do produto a ser editado.
     */
    public function editar(int $id): void
    {
        $produto = (new ProdutoModelo())->buscaPorId($id);

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($dados)) {

            if ($this->validarDados($dados)) {
                $produto = (new ProdutoModelo())->buscaPorId($id);

                $produto->usuario_id = $this->usuario->id;
                $produto->categoria_id = $dados['categoria_id'];
                $produto->slug = Helpers::slug($dados['titulo']);
                $produto->titulo = $dados['titulo'];
                $produto->texto = $dados['texto'];
                $produto->status = $dados['status'];
                $produto->atualizado_em = date('Y-m-d H:i:s');

                //atualizar a capa no DB e no servidor, se um novo arquivo de imagem for enviado
                if (!empty($_FILES['capa']['name'])) {
                    if ($produto->capa && file_exists("uploads/imagens/{$produto->capa}")) {
                        unlink("uploads/imagens/{$produto->capa}");
                        unlink("uploads/imagens/thumbs/{$produto->capa}");
                    }
                    $produto->capa = $this->capa ?? null;
                }

                if ($produto->salvar()) {
                    $this->mensagem->sucesso('Editado com sucesso.')->flash();

                    Helpers::redirecionar('admin/produtos/listar');
                } else {
                    $this->mensagem->erro($produto->erro())->flash();
                    Helpers::redirecionar('admin/produtos/listar');
                }
            }
        }

        echo $this->template->renderizar('produtos/formulario.html', [
            'produto' => $produto,
            'categorias' => (new CategoriaModelo())->busca()->resultado(true)
        ]);
    }

    /**
     * Deletar um produto no painel de administração.
     *
     * Este método lida com a exclusão de um produto pelo ID, mostrando mensagens apropriadas com base no resultado.
     *
     * @param int $id O ID do produto a ser excluído.
     */
    public function deletar(int $id): void
    {
        if (is_int($id)) {
            $produto = (new ProdutoModelo())->buscaPorId($id);
            if (!$produto) {
                $this->mensagem->erro('O produto que você está tentando deletar não existe.')->flash();
                Helpers::redirecionar('admin/produtos/listar');
            } else {
                if ($produto->deletar()) {

                    if ($produto->capa && file_exists("uploads/imagens/{$produto->capa}")) {
                        unlink("uploads/imagens/{$produto->capa}");
                        unlink("uploads/imagens/thumbs/{$produto->capa}");
                    }
                    $this->mensagem->sucesso('Deletado com sucesso.')->flash();
                    Helpers::redirecionar('admin/produtos/listar');
                } else {
                    $this->mensagem->erro($produto->erro())->flash();
                    Helpers::redirecionar('admin/produtos/listar');
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
            $this->mensagem->alerta('Escreva um título para o Post!')->flash();
            return false;
        }
        if (empty($dados['texto'])) {
            $this->mensagem->alerta('Escreva um texto para o Post!')->flash();
            return false;
        }

        if (!empty($_FILES['capa'])) {
            $upload = new Upload($_FILES['capa'], 'pt_BR');
            if ($upload->uploaded) {
                $titulo = $upload->file_new_name_body = Helpers::slug($dados['titulo']);
                $upload->jpeg_quality = 90;
                $upload->image_convert = 'jpg';
                $upload->process('uploads/imagens/');

                if ($upload->processed) {
                    // Redimensionar a imagem para miniatura
                    $this->capa = $upload->file_dst_name;
                    $upload->file_new_name_body = $titulo;
                    $upload->image_resize = true;
                    $upload->image_x = 313;
                    $upload->image_y = 503;
                    $upload->jpeg_quality = 90;
                    $upload->image_convert = 'jpg';
                    $upload->process('uploads/imagens/thumbs/');

                    if ($upload->processed) {
                        $this->capa = $upload->file_dst_name;

                        // Agora, você tem a miniatura disponível para exibição na página

                        $upload->clean();
                    } else {
                        $this->mensagem->alerta($upload->error)->flash();
                        return false;
                    }
                }
            }
        }


        return true;
    }
}
