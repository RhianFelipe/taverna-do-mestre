<?php 
// Front Controller
session_start();
//Para requerir o db, o require é usado para se caso o db falhar, ele de um erro mais critico
require '../src/db/conexao.php';

// Obtém o caminho da URL para o roteamento
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/mestre/public', '', $path);

// Roteamos, ele "puxa" a page requisitada pelo usuario
$viewPath = match ($path) {
    '/', '/inicio' => 'pageHome.php',
    '/magias' => 'pageSpells.php',
    default => '404.php',
};

// Aqui, a página é montada na ordem correta
require '../resources/view/layout/header.php';
require '../resources/view/' . $viewPath;