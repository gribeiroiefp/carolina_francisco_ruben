<?php

$conn = new mysqli('127.0.0.1', 'root', '', 'livros_db');

if ($conn->connect_error) {
    die('Erro na ligação: ' . $conn->connect_error);
}

$sql = 'SELECT * FROM livros ORDER BY ano DESC LIMIT 3';
$livros = $conn->query($sql);

$sql = 'SELECT autores.id, nome, foto, COUNT(*) FROM autores JOIN autores_livros WHERE autores_livros.id_autor = autores.id GROUP BY autores.id LIMIT 3;';
$autores = $conn->query($sql);

?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website de livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <header class="container-fluid">
        <div class="container-lg">
            <div class="row align-items-center">
                <h1 class="col-4">Website de livros</h1>
                <nav class="col text-end">
                    <a href="#">Página inicial</a>
                    <a href="pesquisa.php">Pesquisa</a>
                </nav>
            </div>
        </div>
    </header>

    <br>
    <hr><br>

    <div class="container-lg recent-books">
        <h2>Livros mais recentes</h2>
        <div class="row">
            <?php
            if ($livros && $livros->num_rows > 0) {
                while ($row = $livros->fetch_assoc()) {
                    $id = (int)$row['id'];
                    $titulo = htmlspecialchars($row['titulo']);
                    $ano = (int)$row['ano'];
                    $capa = htmlspecialchars($row['capa']);

                    echo <<<HTML
                    <div class="recente-cont col">
                        <div class="recente container"
                            style="background-image: url('$capa');">
                            <a href="./livro.php?id=$id">
                                <div class="row align-items-end">
                                    <div class="col info-holder-books">
                                        <h3>$titulo</h3>
                                        <p>$ano</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    HTML;
                }
            }
            ?>
        </div>
    </div>

    <br>
    <hr><br>
    <div class="container-lg recent-authors">
        <h2>Autores com mais Livros</h2>
        <div class="row">
            <?php
            if ($autores && $autores->num_rows > 0) {
                while ($row = $autores->fetch_assoc()) {
                    $id = (int)$row['id'];
                    $nome = htmlspecialchars($row['nome']);
                    $foto = htmlspecialchars($row['foto']);

                    echo <<<HTML
                        <div class="recente-cont col">
                            <div class="recente container" style="background-image: url('$foto');">
                                <a href="./autor.php?id=$id">
                                    <div class="row align-items-end">
                                        <div class="col info-holder-authors">
                                            <h3>$nome</h3>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        HTML;
                }
            }
            ?>
        </div>
    </div>

    <br>
    <hr><br>

    <footer class="container-fluid text-center">
        <div class="container-lg">
            <p>&copy;2025 Website de livros</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>

</html>