<?php

namespace sistema\Controlador\Admin;

use sistema\Nucleo\Sessao;
use sistema\Nucleo\Helpers;
use sistema\Modelo\ProdutoModelo;
use sistema\Modelo\UsuarioModelo;
use sistema\Modelo\CategoriaModelo;

/**
 * Classe - Dashboard do painel administrativo.
 *
 * @author Fernando
 */
class AdminDashboard extends AdminControlador
{

    public function dashboard(): void
    {

        $produto = new ProdutoModelo();
        $usuario = new UsuarioModelo();
        $categoria = new CategoriaModelo();

        echo $this->template->renderizar('dashboard.html', [
            'posts' => [
                'posts' => $produto->busca()->ordem('id DESC')->limite(5)->resultado(true),
                'total' => $produto->busca()->total(),
                'ativo' => $produto->busca('status = 1')->total(),
                'inativo' => $produto->busca('status = 0')->total()
                ],
            'produtos' => $produto->busca()->ordem('status ASC, id DESC')->resultado(true),
            'produto' => [
                'total' => $produto->total(),
                'ativo' => $produto->busca('status = 1')->total(),
                'inativo' => $produto->busca('status = 0')->total()
            ],
            'usuarios' => $usuario->busca()->resultado(true),
            'usuario' => [
                'login' => $usuario->busca()->ordem('ultimo_login DESC')->limite(5)->resultado(true),
                'total' => $usuario->total(),
                'ativo' => $usuario->busca('status = 1')->total(),
                'inativo' => $usuario->busca('status = 0')->total(),
            ],
            'categorias' => $categoria->busca()->resultado(true),
            'categoria' => [
                'total' => $categoria->total(),
                'ativo' => $categoria->busca('status = 1')->total(),
                'inativo' => $categoria->busca('status = 0')->total(),
            ],
            'ultimas_categorias' => $categoria->busca()->ordem('titulo DESC')->limite(5)->resultado(true),
        ]);
    }

    public function sair(): void
    {
        $sessao = new Sessao();
        $sessao->limpar('usuarioId');
        $this->mensagem->informa('Você saiu do painel de controle.')->flash();
        Helpers::redirecionar('admin/login');
    }
}
