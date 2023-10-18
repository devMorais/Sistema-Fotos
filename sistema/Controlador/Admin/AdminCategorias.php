<?php

namespace sistema\Controlador\Admin;

use sistema\Modelo\CategoriaModelo;
use sistema\Nucleo\Helpers;
use Verot\Upload\Upload;

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

    private string $capa;

    /**
     * Lista todas as categorias no painel de administração.
     */
    public function listar(): void
    {
        $categoria = new CategoriaModelo();
        echo $this->template->renderizar('categorias/listar.html', [
            'categorias' => $categoria->busca()->ordem('id DESC, titulo ASC')->resultado(true),
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

                $categoria->usuario_id = $this->usuario->id;
                $categoria->capa_ativa = $dados['capa_ativa'];
                $categoria->url = $dados['url'];

                $categoria->slug = Helpers::slug($dados['titulo']);
                $categoria->titulo = $dados['titulo'];
                $categoria->texto = $dados['texto'];
                $categoria->status = $dados['status'];
                $categoria->capa = $this->capa ?? null;

                if ($categoria->salvar()) {
                    $this->mensagem->sucesso('Categoria cadastrada com sucesso')->flash();
                    Helpers::redirecionar('admin/categorias/listar');
                } else {
                    $this->mensagem->erro($categoria->erro())->flash();
                    Helpers::redirecionar('admin/categorias/listar');
                }
            }
        }

        echo $this->template->renderizar('categorias/formulario.html', [
            'categorias' => $dados
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
            if ($this->validarDados($dados)) {
                $categoria = (new CategoriaModelo())->buscaPorId($categoria->id);

                $categoria->usuario_id = $this->usuario->id;
                $categoria->capa_ativa = $dados['capa_ativa'];
                $categoria->slug = Helpers::slug($dados['titulo']);
                $categoria->titulo = $dados['titulo'];
                $categoria->texto = $dados['texto'];
                $categoria->status = $dados['status'];
                $categoria->atualizado_em = date('Y-m-d H:i:s');

                //atualizar a capa no DB e no servidor, se um novo arquivo de imagem for enviado
                if (!empty($_FILES['capa']['name'])) {
                    if ($categoria->capa && file_exists("uploads/imagens/{$categoria->capa}")) {
                        unlink("uploads/imagens/{$categoria->capa}");
                        unlink("uploads/imagens/thumbs/{$categoria->capa}");
                    }
                    $categoria->capa = $this->capa ?? null;
                }

                if ($categoria->salvar()) {
                    $this->mensagem->sucesso('Categoria atualizada com sucesso')->flash();
                    Helpers::redirecionar('admin/categorias/listar');
                } else {
                    $this->mensagem->erro($categoria->erro())->flash();
                    Helpers::redirecionar('admin/categorias/listar');
                }
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
                if ($categoria->deletar()) {

                    if ($categoria->capa && file_exists("uploads/imagens/{$categoria->capa}")) {
                        unlink("uploads/imagens/{$categoria->capa}");
                        unlink("uploads/imagens/thumbs/{$categoria->capa}");
                    }

                    $this->mensagem->sucesso('Deletado com sucesso.')->flash();
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
