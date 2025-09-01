<?php

$conn = new mysqli('127.0.0.1', 'root', '', 'livros_db');

if ($conn->connect_error) {
    die('Erro na ligação: ' . $conn->connect_error);
}

$sql = 'SELECT * FROM livros ORDER BY ano DESC LIMIT 3';
$livros = $conn->query($sql);

$sql = 'SELECT autores.id, nome, foto, COUNT(*) FROM autores JOIN livro_autor WHERE autores_livros.id_autor = autores.id GROUP BY autores.id LIMIT 3;';
$autores = $conn->query($sql);

?>

<html>

<head>
</head>

<body>
    <h1>Website de livros</h1>
</body>

</html>