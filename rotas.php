<?php

use Pecee\SimpleRouter\SimpleRouter;
use sistema\Nucleo\Helpers;

try {
    SimpleRouter::setDefaultNamespace('sistema\Controlador');

    SimpleRouter::get(URL_SITE, 'SiteControlador@index');
    SimpleRouter::get(URL_SITE . 'sobre-nos', 'SiteControlador@sobre');
    SimpleRouter::get(URL_SITE . 'produto/{id}', 'SiteControlador@produtoPorId');
    SimpleRouter::get(URL_SITE . 'categoria/{id}', 'SiteControlador@categoriaPorId');
    SimpleRouter::post(URL_SITE . 'buscar', 'SiteControlador@buscar');
    SimpleRouter::get(URL_SITE . 'contato', 'SiteControlador@contato');

    SimpleRouter::get(URL_SITE . '404', 'SiteControlador@erro404');

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

        //ADMIN CATEGORIAS
        SimpleRouter::get(URL_ADMIN . 'categorias/listar', 'AdminCategorias@listar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'categorias/cadastrar', 'AdminCategorias@cadastrar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'categorias/editar/{id}', 'AdminCategorias@editar');
        SimpleRouter::get(URL_ADMIN . 'categorias/deletar/{id}', 'AdminCategorias@deletar');
    });

    SimpleRouter::start();
} catch (Pecee\SimpleRouter\Exceptions\NotFoundHttpException $ex) {
    if (Helpers::localhost()) {
        echo $ex;
    } else {
        Helpers::redirecionar('404');
    }
}