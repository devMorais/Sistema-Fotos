<?php

namespace sistema\Modelo;

use sistema\Nucleo\Conexao;
use sistema\Nucleo\Modelo;

/**
 * Classe Modelo - Categorias
 *
 * @author Fernando
 */
class CategoriaModelo extends Modelo
{

    public function __construct()
    {
        parent::__construct('categorias');
    }

    public function produtos(int $id): ?array
    {
        $busca = (new ProdutoModelo())->busca("categoria_id = {$id}");
        return $busca->resultado(true);
    }
}
