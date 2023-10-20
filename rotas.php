<?php

use Pecee\SimpleRouter\SimpleRouter;
use sistema\Nucleo\Helpers;

try {
    //namespace dos controladores
    SimpleRouter::setDefaultNamespace('sistema\Controlador');

    SimpleRouter::get(URL_SITE, 'SiteControlador@index');
    SimpleRouter::get(URL_SITE . 'sobre-nos', 'SiteControlador@sobre');
    SimpleRouter::get(URL_SITE . 'produto/{categoria}/{slug}', 'SiteControlador@produtoPorId');
    SimpleRouter::get(URL_SITE . 'categoria/{slug}/{pagina?}', 'SiteControlador@categoriaPorId');
    SimpleRouter::post(URL_SITE . 'buscar', 'SiteControlador@buscar');
    SimpleRouter::get(URL_SITE . 'contato', 'SiteControlador@contato');
    SimpleRouter::get(URL_SITE . '404', 'SiteControlador@erro404');

    //USUARIO
    SimpleRouter::match(['get', 'post'], URL_SITE . 'cadastro', 'UsuarioControlador@cadastro');
    SimpleRouter::match(['get', 'post'], URL_SITE . 'usuario/confirmar/email/{token}', 'UsuarioControlador@confirmarEmail');

    //ROTAS ADMIN
    SimpleRouter::group(['namespace' => 'Admin'], function () {

        //ADMIN LOGIN
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'login', 'AdminLogin@login');

        //DASHBOARD
        SimpleRouter::get(URL_ADMIN . 'dashboard', 'AdminDashboard@dashboard');
        SimpleRouter::get(URL_ADMIN . 'sair', 'AdminDashboard@sair');

        //ADMIN PRODUTOS
        SimpleRouter::get(URL_ADMIN . 'produtos/listar', 'AdminProdutos@listar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'produtos/cadastrar', 'AdminProdutos@cadastrar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'produtos/editar/{id}', 'AdminProdutos@editar');
        SimpleRouter::get(URL_ADMIN . 'produtos/deletar/{id}', 'AdminProdutos@deletar');
        SimpleRouter::get(URL_ADMIN . 'produtos/datatable', 'AdminProdutos@datatable');

        //ADMIN CATEGORIAS
        SimpleRouter::get(URL_ADMIN . 'categorias/listar', 'AdminCategorias@listar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'categorias/cadastrar', 'AdminCategorias@cadastrar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'categorias/editar/{id}', 'AdminCategorias@editar');
        SimpleRouter::get(URL_ADMIN . 'categorias/deletar/{id}', 'AdminCategorias@deletar');

        //ADMIN USUARIOS
        SimpleRouter::get(URL_ADMIN . 'usuarios/listar', 'AdminUsuarios@listar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'usuarios/cadastrar', 'AdminUsuarios@cadastrar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'usuarios/editar/{id}', 'AdminUsuarios@editar');
        SimpleRouter::get(URL_ADMIN . 'usuarios/deletar/{id}', 'AdminUsuarios@deletar');
        SimpleRouter::post(URL_ADMIN . 'posts/datatable', 'AdminPosts@datatable');
    });

    SimpleRouter::start();
} catch (Pecee\SimpleRouter\Exceptions\NotFoundHttpException $ex) {
    if (Helpers::localhost()) {
        echo $ex->getMessage();
    } else {
        Helpers::redirecionar('404');
    }
}