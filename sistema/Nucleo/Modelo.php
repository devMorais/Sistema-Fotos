<?php

namespace sistema\Nucleo;

use sistema\Nucleo\Conexao;
use sistema\Nucleo\Mensagem;

/**
 * Classe Modelo
 *
 * Esta classe serve como um modelo de base para outras classes de modelo no sistema.
 * Ela fornece funcionalidades comuns, como busca, inserção, atualização e exclusão de registros no banco de dados.
 *
 * @package sistema\Nucleo
 * @author Fernando
 */
abstract class Modelo
{
    protected $dados;
    protected $query;
    protected $erro;
    protected $parametros;
    protected $tabela;
    protected $ordem;
    protected $limite;
    protected $offset;
    protected $mensagem;

    /**
     * Construtor da classe Modelo.
     *
     * @param string $tabela O nome da tabela no banco de dados associada a este modelo.
     */
    public function __construct(string $tabela)
    {
        $this->tabela = $tabela;
        $this->mensagem = new Mensagem();
    }

    /**
     * Define a ordem dos resultados da consulta.
     *
     * @param string $ordem A cláusula de ordem (ex. "campo ASC" ou "campo DESC").
     * @return $this
     */
    public function ordem(string $ordem)
    {
        $this->ordem = " ORDER BY {$ordem}";
        return $this;
    }

    /**
     * Define o limite de resultados da consulta.
     *
     * @param string $limite O número máximo de resultados a serem retornados.
     * @return $this
     */
    public function limite(string $limite)
    {
        $this->limite = " LIMIT {$limite}";
        return $this;
    }

    /**
     * Define o deslocamento de resultados da consulta.
     *
     * @param string $offset O deslocamento de resultados a partir do início.
     * @return $this
     */
    public function offset(string $offset)
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * Retorna o erro da última operação.
     *
     * @return mixed O erro da última operação, se houver.
     */
    public function erro()
    {
        return $this->erro;
    }

    /**
     * Retorna a instância da classe de mensagens.
     *
     * @return Mensagem A instância da classe de mensagens.
     */
    public function mensagem()
    {
        return $this->mensagem;
    }

    /**
     * Retorna os dados do registro atual.
     *
     * @return mixed Os dados do registro atual.
     */
    public function dados()
    {
        return $this->dados;
    }

    /**
     * Define um valor para uma propriedade dinamicamente.
     *
     * @param string $nome O nome da propriedade.
     * @param mixed $valor O valor a ser definido.
     */
    public function __set($nome, $valor)
    {
        if (empty($this->dados)) {
            $this->dados = new \stdClass();
        }

        return $this->dados->$nome = $valor;
    }

    /**
     * Verifica se uma propriedade está definida.
     *
     * @param string $nome O nome da propriedade.
     * @return bool true se a propriedade estiver definida, false caso contrário.
     */
    public function __isset($nome)
    {
        return isset($this->dados->$nome);
    }

    /**
     * Retorna o valor de uma propriedade dinamicamente.
     *
     * @param string $nome O nome da propriedade.
     * @return mixed O valor da propriedade, ou null se não estiver definida.
     */
    public function __get($nome)
    {
        return $this->dados->$nome ?? null;
    }

    /**
     * Inicia uma consulta ao banco de dados com cláusulas WHERE e parâmetros.
     *
     * @param string|null $termos A cláusula WHERE da consulta.
     * @param string|null $parametros Os parâmetros da consulta.
     * @param string $colunas As colunas a serem selecionadas (padrão: '*').
     * @return $this
     */
    public function busca(?string $termos = null, ?string $parametros = null, string $colunas = '*')
    {
        if ($termos) {
            $this->query = "SELECT {$colunas} FROM " . $this->tabela . " WHERE {$termos} ";
            parse_str($parametros, $this->parametros);
            return $this;
        }

        $this->query = "SELECT {$colunas} FROM " . $this->tabela;
        return $this;
    }

    /**
     * Retorna o resultado da consulta ao banco de dados.
     *
     * @param bool $todos true para obter todos os resultados, false para obter apenas o primeiro.
     * @return mixed Os resultados da consulta ou null em caso de erro.
     */
    public function resultado(bool $todos = false)
    {
        try {
            $stmt = Conexao::getInstancia()->prepare($this->query . $this->ordem . $this->limite . $this->offset);
            $stmt->execute($this->parametros);

            if (!$stmt->rowCount()) {
                return null;
            }

            if ($todos) {
                return $stmt->fetchAll();
            }

            return $stmt->fetchObject(static::class);
        } catch (\PDOException $ex) {
            $this->erro = $ex;
            return null;
        }
    }

    /**
     * Método protegido para inserir um novo registro no banco de dados.
     *
     * @param array $dados Os dados a serem inseridos.
     * @return mixed O ID do novo registro inserido ou null em caso de erro.
     */
    protected function cadastrar(array $dados)
    {
        try {
            $colunas = implode(',', array_keys($dados));
            $valores = ':' . implode(',:', array_keys($dados));

            $query = "INSERT into " . $this->tabela . " ($colunas) VALUES ({$valores})";
            $stmt = Conexao::getInstancia()->prepare($query);
            $stmt->execute($this->filtro($dados));

            return Conexao::getInstancia()->lastInsertId();
        } catch (\PDOException $ex) {
            $this->erro = $ex;
            return null;
        }
    }

    /**
     * Método protegido para atualizar um registro no banco de dados.
     *
     * @param array $dados Os dados a serem atualizados.
     * @param string $termos A cláusula WHERE para identificar o registro a ser atualizado.
     * @return mixed O número de registros atualizados ou null em caso de erro.
     */
    protected function atualizar(array $dados, string $termos)
    {
        try {
            $set = [];
            foreach ($dados as $chave => $valor) {
                $set[] = "{$chave} = :{$chave}";
            }
            $set = implode(',', $set);

            $query = "UPDATE " . $this->tabela . " SET {$set} WHERE {$termos}";

            $stmt = Conexao::getInstancia()->prepare($query);
            $stmt->execute($this->filtro($dados));

            return ($stmt->rowCount() ?? 1);
        } catch (\PDOException $ex) {
            $this->erro = $ex;
            return null;
        }
    }

    /**
     * Método privado para aplicar um filtro aos dados.
     *
     * @param array $dados Os dados a serem filtrados.
     * @return array Os dados filtrados.
     */
    private function filtro(array $dados)
    {
        $filtro = [];

        foreach ($dados as $chave => $valor) {
            $filtro[$chave] = (is_null($valor) ? null : filter_var($valor, FILTER_DEFAULT));
        }

        return $filtro;
    }

    /**
     * Método protegido para converter os dados do modelo em um array.
     *
     * @return array Os dados do modelo em forma de array.
     */
    protected function armazenar()
    {
        $dados = (array) $this->dados;
        return $dados;
    }

    /**
     * Busca um registro no banco de dados com base em seu ID.
     *
     * @param int $id O ID do registro a ser buscado.
     * @return mixed O registro encontrado ou null em caso de erro.
     */
    public function buscaPorId(int $id)
    {
        $busca = $this->busca("id = {$id}");
        return $busca->resultado();
    }

    /**
     * Apaga registros do banco de dados com base em termos de consulta.
     *
     * @param string $termos A cláusula WHERE para identificar os registros a serem apagados.
     * @return bool true se a operação for bem-sucedida, false em caso de erro.
     */
    public function apagar(string $termos)
    {
        try {
            $query = "DELETE FROM " . $this->tabela . " WHERE {$termos}";

            $stmt = Conexao::getInstancia()->prepare($query);
            $stmt->execute();

            return true;
        } catch (\PDOException $ex) {
            $this->erro = $ex->getMessage();
            return null;
        }
    }

    /**
     * Deleta o registro atual do modelo do banco de dados.
     *
     * @return bool true se a operação for bem-sucedida, false em caso de erro.
     */
    public function deletar()
    {
        if (empty($this->id)) {
            return false;
        }

        $deletar = $this->apagar("id = {$this->id}");
        return $deletar;
    }

    /**
     * Retorna o número total de registros na tabela.
     *
     * @return int O número total de registros.
     */
    public function total(): int
    {
        $stmt = Conexao::getInstancia()->prepare($this->query);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Salva os dados do modelo no banco de dados.
     *
     * Este método decide se deve cadastrar um novo registro ou atualizar um existente com base no ID do modelo.
     * Em seguida, atualiza os dados do modelo com os dados recém-salvos do banco de dados.
     *
     * @return bool true se a operação for bem-sucedida, false em caso de erro.
     */
    public function salvar()
    {
        // CADASTRAR
        if (empty($this->id)) {
            $id = $this->cadastrar($this->armazenar());
            if ($this->erro) {
                $this->mensagem->erro('Erro de sistema ao tentar cadastrar os dados')->flash();
                return false;
            }
        }

        // ATUALIZAR
        if (!empty($this->id)) {
            $id = $this->id;
            $this->atualizar($this->armazenar(), "id = {$id}");
            if ($this->erro) {
                $this->mensagem->erro('Erro de sistema ao tentar atualizar os dados')->flash();
                return false;
            }
        }

        $this->dados = $this->buscaPorId($id)->dados();

        return true;
    }
}
