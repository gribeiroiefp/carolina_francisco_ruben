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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website de livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>

<body>
    <h1>Website de livros</h1>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>

</html>