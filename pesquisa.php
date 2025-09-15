<?php

$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$resultados = [];

if (isset($_GET['string']) && !empty($_GET['string'])) {
    $string = $_GET['string'];
    $sql = "SELECT   livros.id, livros.titulo, livros.capa, livros.ano
    FROM livros
    LEFT JOIN autores_livros ON livros.id = autores_livros.id_livro
    LEFT JOIN autores  ON autores.id = autores_livros.id_autor
    WHERE livros.titulo LIKE '%$string%' OR autores.nome LIKE '%$string%'";

    $resultados = mysqli_query($conn, $sql);
   

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livros</title>
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
                    <a href="index.php">Página inicial</a>
                    <a href="pesquisa.php">Pesquisa</a>
                </nav>
            </div>
        </div>
    </header>
    <div class="container-lg pesquisa">
        <div class="pesquisa-form">
            <h2>Pesquisa</h2>
            <form action="" method="GET">
                <div class="sombra-form">
                    <?php if (isset($_GET['string'])): ?>
                        <input type="text" name="string" placeholder="Procurar por título ou Autor" required value="<?= $_GET['string'] ?>">
                    <?php else: ?>
                        <input type="text" name="string" placeholder="Procurar por título ou Autor " required>
                    <?php endif; ?>
                    <button type="submit">Pesquisar</button>
                </div>
            </form>
        </div>
        <?php if (isset($_GET['string']) && !$_GET['string'] == ''): ?>
            <div class="lista col">
                <h3>Resultados da pesquisa de Livros</h3>
                <?php
                if ($resultados && mysqli_num_rows($resultados) > 0) {
                    while ($livro = mysqli_fetch_assoc($resultados)) {
                        $id = $livro['id'];
                        $capa = $livro['capa'];
                        $titulo = htmlspecialchars($livro['titulo']);
                        $ano = htmlspecialchars($livro['ano']);

                        echo <<<HTML
                    <a href="livro.php?id=$id" class="livro_link row align-items-end">
                        <img src="$capa" alt="o padrinho" class="col-3">
                        <div class="livro col">
                            <h3>$titulo</h3>
                            <p>$ano</p>
                        </div>
                    </a>
                    HTML;
                    }
                }
                ?>
            </div>
        <?php endif; ?>
        <div class="editar">
            <h2>Opções</h2>
            <a href="inserir_livro.php" class="btn btn-secondary">Inserir Livro</a>
            <a href="inserir_autor.php" class="btn btn-secondary">Inserir Autor</a>
            <a href="editar_livro.php" class="btn btn-secondary">Editar Livro</a>
            <a href="editar_autor.php" class="btn btn-secondary">Editar Autor</a>


        </div>
    </div>
    <footer class="container-fluid text-center">
        <div class="container-lg">
            <p>&copy; 2025 Website de livros</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>

</html>