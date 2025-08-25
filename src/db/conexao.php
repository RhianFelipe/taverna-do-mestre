<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'masterhelp'; 

try {
    // Cria uma nova instância da classe PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);

    // Define o modo de erro para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Em caso de erro, exibe a mensagem
    die("Erro de conexão: " . $e->getMessage());
}

?>