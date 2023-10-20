<?php

namespace sistema\Suporte;

use Twig\Lexer;
use sistema\Nucleo\Helpers;
use sistema\Controlador\UsuarioControlador;

/**
 * Classe Template
 */
class Template
{

    private \Twig\Environment $twig;

    public function __construct(string $diretorio)
    {
        $loader = new \Twig\Loader\FilesystemLoader($diretorio);
        $this->twig = new \Twig\Environment($loader);

        $lexer = new Lexer($this->twig, array(
            $this->helpers()
        ));
        $this->twig->setLexer($lexer);
    }

    /**
     * Metodo responsavel por realizar a renderização das views
     * @param string $view
     * @param array $dados
     * @return string
     */
    public function renderizar(string $view, array $dados): string
    {
        return $this->twig->render($view, $dados);
    }

    /**
     * Metodo responsavel por chamar funções da classe Helpers
     * @return void
     */
    private function helpers(): void
    {
        array(
            $this->twig->addFunction(
                    new \Twig\TwigFunction('url', function (string $url = null) {
                                return Helpers::url($url);
                            })
            ),
            $this->twig->addFunction(
                    new \Twig\TwigFunction('saudacao', function () {
                                return Helpers::saudacao();
                            })
            ),
            $this->twig->addFunction(
                    new \Twig\TwigFunction('resumirTexto', function (string $texto, int $limite) {
                                return Helpers::resumirTexto($texto, $limite);
                            })
            ),
            $this->twig->addFunction(
                    new \Twig\TwigFunction('flash', function () {
                                return Helpers::flash();
                            })
            ),
            $this->twig->addFunction(
                    new \Twig\TwigFunction('usuario', function () {
                                return UsuarioControlador::usuario();
                            })
            ),
            $this->twig->addFunction(
                    new \Twig\TwigFunction('contarTempo', function (string $data = null) {
                                return Helpers::contarTempo($data);
                            })
            ),
            $this->twig->addFunction(
                    new \Twig\TwigFunction('formatarNumero', function (int $numero) {
                                return Helpers::formatarNumero($numero);
                            })
            ),
            $this->twig->addFunction(
                    new \Twig\TwigFunction('tempoCarregamento', function () {

                                $tempoTotal = microtime(true) - filter_var($_SERVER["REQUEST_TIME_FLOAT"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                                return number_format($tempoTotal, 2);
                            })
            ),
        );
    }
}
