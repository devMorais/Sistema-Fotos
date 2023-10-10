<?php

//Arquivo index responsável pela inicialização do sistema
require 'vendor/autoload.php';
//require 'rotas.php';

$sessao = new \sistema\Nucleo\Sessao();

//$sessao->criar('usuario', ['id' =>10, 'nome' => 'Fernando Aguiar']);


var_dump($sessao->carregar());
echo '<hr>';
var_dump($sessao->checar('visitas'));
echo '<hr>';
$sessao->limpar('visitas');
var_dump($sessao->checar('visitas'));
echo '<hr>';
