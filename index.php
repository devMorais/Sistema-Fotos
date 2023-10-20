<?php

//Arquivo index responsável pela inicialização do sistema
require 'vendor/autoload.php';

use sistema\Modelo\ProdutoModelo;

require 'rotas.php';

//echo $pagina = (filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) ?? 1);
//
//$limite = 5;
//$offset = ($pagina - 1) * $limite;
//echo '<hr>';
//
//$produtos = (new ProdutoModelo());
//
//$total = $produtos->busca(null, 'COUNT(id)', 'id')->total();
//
////$paginar = array_slice($produtos, $offset, $limite);
//$paginar = $produtos->busca()->limite($limite)->offset($offset)->resultado(true);
//
//$total = ceil($total / $limite);
////$total = ceil(count($produtos) / $limite);
//
//foreach ($paginar as $produto) {
//    echo $produto->id . ' - ' . $produto->titulo . '<br><br>';
//}
//
//echo '<hr>';
//echo "Página {$pagina} de {$total}";
//echo '<hr>';
//
//if ($pagina > 1) {
//    echo '<a href="?pagina=' . ($pagina - 1) . ' ">Anterior   </a>';
//}
//
//$inicio = max(1, $pagina - 3);
//$fim = min($total, $pagina + 3);
//
//for ($i = $inicio; $i <= $fim; $i++) {
//    if ($i == $pagina) {
//        echo $i . '  ';
//    } else {
//        echo '<a href="?pagina=' . $i . '">' . $i . '</a> ';
//    }
//}
//
//if ($pagina < $total) {
//    echo '<a href="?pagina=' . ($pagina + 1) . '">Próximo</a>';
//}
//
//
