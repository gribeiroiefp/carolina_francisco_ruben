<?php

$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$id = (int) $_GET['id'];

// // Handle deletion of the movie
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_movie'])) {
//     mysqli_query($conn, "DELETE FROM filmes WHERE id = $id");
//     header('Location: index.php');
//     exit;
// }

// // Handle adding an actor to this movie
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_actor'])) {
//     $id_ator = (int) $_POST['id_ator'];
//     $personagem = mysqli_real_escape_string($conn, $_POST['personagem']);

//     if ($id_ator > 0 && $personagem !== '') {
//         $sql_insert = "INSERT INTO filme_ator (id_filme, id_ator, personagem) 
//                        VALUES ($id, $id_ator, '$personagem')";
//         mysqli_query($conn, $sql_insert);
//     }
// }

// Fetch book info
$sql = "SELECT * FROM livros WHERE id = $id";
$resultado = mysqli_query($conn, $sql);
$livro = mysqli_fetch_assoc($resultado);

// Fetch authors already assigned to this book
$sql_autores = "SELECT * FROM autores 
               JOIN autores_livros ON autores.id = autores_livros.id_autor
               WHERE autores_livros.id_livro = $id";
$resultado_autores = mysqli_query($conn, $sql_autores);

// // Fetch all actors for the dropdown
// $sql_todos_atores = "SELECT id, nome FROM atores ORDER BY nome ASC";
// $resultado_todos_atores = mysqli_query($conn, $sql_todos_atores);

// Fetch all authors for the dropdown (all except already)
$sql_todos_autores = "SELECT id, nome 
FROM autores 
WHERE NOT EXISTS (
    SELECT 1 
    FROM autores_livros 
    WHERE autores_livros.id_autor = autores.id 
    AND autores_livros.id_livro = $id
);";

$resultado_todos_autores = mysqli_query($conn, $sql_todos_autores);

// Handle adding an author to this book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_author'])) {
    $id_autor = (int) $_POST['id_author'];
    echo $id_autor;
    if ($id_autor > 0) {
        $sql_insert = "INSERT INTO autores_livros (id_livro, id_autor) VALUES ($id, $id_autor)";
        mysqli_query($conn, $sql_insert);
    }
}

?>

<!DOCTYPE html>
<html lang="pt">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livro</title>
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

    <div class="container-lg filme">
        <div class="row align-items-start info">
            <img src="<?php echo htmlspecialchars($livro['capa']); ?>" alt="" class="col-3">
            <div class="col-8">
                <h2><?php echo htmlspecialchars($livro['titulo']); ?></h2>
                <p><span class="rotulo ano">Ano:</span> <?php echo htmlspecialchars($livro['ano']); ?></p>
            </div>
            <!-- <div class="col-1 opcoes align-self-start">
                <a href="editar_filme.php?id=<? //= $livro['id'] 
                                                ?>" class="btn btn-primary btn-editar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z" />
                    </svg>
                </a>
                <form method="post" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja apagar este filme?');">
                    <input type="hidden" name="delete_movie" value="1">
                    <button type="submit" class="btn btn-danger btn-remover"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                        </svg></button>
                </form>
            </div> -->
        </div>

        <div class="informacao">
            <div class="lista col">
                <h3>Autores</h3>
                <?php
                if ($resultado_autores && mysqli_num_rows($resultado_autores) > 0) {
                    while ($autor = mysqli_fetch_assoc($resultado_autores)) {
                        $id_autor = $autor['id_autor'];
                        $nome = htmlspecialchars($autor['nome']);
                        $foto = htmlspecialchars($autor['foto']);
                        $nacionalidade = htmlspecialchars($autor['nacionalidade']);
                        echo <<<HTML
                    <a href="autor.php?id=$id_autor" class="row align-items-end mb-2">
                        <img src="$foto" alt="$nome" class="col-2">
                        <div class="col-10">
                            <p class="nome mb-0">$nome</p>
                            <p class="nacionalidade">$nacionalidade</p>
                        </div>
                    </a>
                    HTML;
                    }
                } else {
                    echo "<p>Nenhum autor associado a este livro.</p>";
                }
                ?>
            </div>
        </div>

        <!-- Form to add author to this book -->
        <div class="mt-4">
            <h3>Adicionar autor a este livro</h3>
            <form method="post" class="row g-3">
                <input type="hidden" name="add_author" value="1">
                <div class="col-5">
                    <label for="id_author" class="form-label">Autor</label>
                    <select name="id_author" id="id_author" class="form-select" required>
                        <option value="">Selecione um autor</option>
                        <?php
                        while ($autor = mysqli_fetch_assoc($resultado_todos_autores)) {
                            $idA = $autor['id'];
                            $nomeA = htmlspecialchars($autor['nome']);
                            echo "<option value='$idA'>$nomeA</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-2 align-self-end">
                    <button type="submit" class="btn btn-success w-100">Adicionar</button>
                </div>
            </form>
        </div>

        <!-- <div class="editar mt-5">
            <h2>Opções</h2>
            <a href="inserir_filme.php" class="btn btn-primary">Inserir Filme</a>
            <a href="inserir_ator.php" class="btn btn-primary">Inserir Ator</a>
        </div> -->
    </div>

    <footer class="container-fluid text-center mt-5">
        <div class="container-lg">
            <p>&copy;2025 Website de livros</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>