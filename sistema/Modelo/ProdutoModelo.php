<?php

namespace sistema\Modelo;

use sistema\Nucleo\Conexao;
use sistema\Nucleo\Modelo;

/**
 * Description of ProdutoModelo
 *
 * @author Fernando
 */
class ProdutoModelo extends Modelo
{

    public function __construct()
    {
        parent::__construct('produtos');
    }
}
