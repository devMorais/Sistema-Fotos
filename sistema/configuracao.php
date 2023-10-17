
<?php

use sistema\Nucleo\Helpers;

//Arquivo de configuração do sistema
//define o fuso horario
date_default_timezone_set('America/Sao_Paulo');

//informações do sistema
define('SITE_NOME', 'ModaPraia');
define('SITE_DESCRICAO', 'DevFernando - Tecnologia em Sistemas');

//urls do sistema
define('URL_PRODUCAO', 'https://sempreumbug.com.br');
define('URL_DESENVOLVIMENTO', 'http://localhost/blog');

if (Helpers::localhost()) {
    //dados de acesso ao banco de dados em localhost
    define('DB_HOST', 'localhost');
    define('DB_PORTA', '3306');
    define('DB_NOME', 'moda');
    define('DB_USUARIO', 'root');
    define('DB_SENHA', '');

    define('URL_SITE', 'blog/');
    define('URL_ADMIN', 'blog/admin/');
} else {
    //dados de acesso ao banco de dados na hospedagem
    define('DB_HOST', 'localhost');
    define('DB_PORTA', '3306');
    define('DB_NOME', 'u846585591_foto');
    define('DB_USUARIO', 'u846585591_foto');
    define('DB_SENHA', 'qXE*Xn7]F');

    define('URL_SITE', '/');
    define('URL_ADMIN', '/admin/');
}




