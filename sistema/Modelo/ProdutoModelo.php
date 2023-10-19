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
        parent::__construct('produtos_fake1');
    }

    public function categoria(): ?CategoriaModelo
    {
        if ($this->categoria_id) {
            return (new CategoriaModelo())->buscaPorId($this->categoria_id);
        }

        return null;
    }

    public function usuario(): ?UsuarioModelo
    {
        if ($this->usuario_id) {
            return (new UsuarioModelo())->buscaPorId($this->usuario_id);
        }

        return null;
    }

    public function salvar(): bool
    {
        $this->slug();
        return parent::salvar();
    }
}
