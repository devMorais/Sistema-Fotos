<?php

namespace sistema\Controlador\Admin;

use sistema\Nucleo\Sessao;
use sistema\Nucleo\Helpers;
use sistema\Modelo\ProdutoModelo;
use sistema\Modelo\UsuarioModelo;
use sistema\Modelo\CategoriaModelo;

/**
 * Classe AdminDashboard
 *
 * @author Fernando 
 */
class AdminDashboard extends AdminControlador
{

    /**
     * Home do admin
     * @return void
     */
    public function dashboard(): void
    {
        $produtos = new ProdutoModelo();
        $usuarios = new UsuarioModelo();
        $categorias = new CategoriaModelo();

        echo $this->template->renderizar('dashboard.html', [
            'produtos' => [
                'produtos' => $produtos->busca()->ordem('id DESC')->limite(5)->resultado(true),
                'total' => $produtos->busca(null,'COUNT(id)','id')->total(),
                'ativo' => $produtos->busca('status = :s','s=1 COUNT(status)','status')->total(),
                'inativo' => $produtos->busca('status = :s','s=0 COUNT(status)','status')->total()
            ],
            'categorias' => [
                'categorias' => $categorias->busca()->ordem('id DESC')->limite(5)->resultado(true),
                'total' => $categorias->busca()->total(),
                'categoriasAtiva' => $categorias->busca('status = 1')->total(),
                'categoriasInativa' => $categorias->busca('status = 0')->total(),
            ],
            'usuarios' => [
                'logins' => $usuarios->busca()->ordem('ultimo_login DESC')->limite(5)->resultado(true),
                'usuarios' => $usuarios->busca('level != 3')->total(),
                'usuariosAtivo' => $usuarios->busca('status = 1 AND level != 3')->total(),
                'usuariosInativo' => $usuarios->busca('status = 0 AND level != 3')->total(),
                'admin' => $usuarios->busca('level = 3')->total(),
                'adminAtivo' => $usuarios->busca('status = 1 AND level = 3')->total(),
                'adminInativo' => $usuarios->busca('status = 0 AND level = 3')->total()
            ],
        ]);
    }

    /**
     * Faz logout do usuário
     * @return void
     */
    public function sair(): void
    {
        $sessao = new Sessao();
        $sessao->limpar('usuarioId');

        $this->mensagem->informa('Você saiu do painel de controle!')->flash();
        Helpers::redirecionar('admin/login');
    }

}
