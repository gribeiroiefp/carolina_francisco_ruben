<?php

$conn = new mysqli('127.0.0.1', 'root', '', 'livros_db');

if ($conn->connect_error) {
    die('Erro na ligação: ' . $conn->connect_error);
}


$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $ano = (int)$_POST['ano'];

    $diretorio_capas = 'uploads/capas/';

    // Verificar diretório 
    if (!is_dir($diretorio_capas)) {
        mkdir($diretorio_capas, 0755, true);
    }

    $imagem = $_FILES['capa'];
    $fileName = basename($imagem['name']);
    $imagem_caminho = $diretorio_capas . $fileName;

    // Validar é imagem
    $check = getimagesize($imagem['tmp_name']);
    if ($check == false) {
        $msg = "O ficheiro enviado não é uma imagem valida";
    } else {
        if (move_uploaded_file($imagem['tmp_name'], $imagem_caminho)) {
            $sql = 'INSERT INTO livros (titulo, ano, capa) VALUES (?, ?, ?)';
            $query = mysqli_prepare($conn, $sql);
            if ($query) {
                mysqli_stmt_bind_param($query, 'sis', $titulo, $ano, $imagem_caminho);
                if (mysqli_stmt_execute($query)) {
                    $msg = 'Livro inserido com sucesso!';
                } else {
                    $msg = 'Erro ao inserir livro: ' . mysqli_error($conn);
                }
                mysqli_stmt_close($query);
            } else {
                $msg = 'Erro na preparação da query: ' . mysqli_error($conn);
            }
        }
    }
}

// procurar autores 
            $result = $conn->query("SELECT id, nome FROM autores ORDER BY nome");
            $autores = $result->fetch_all(MYSQLI_ASSOC);

mysqli_close($conn);

?>
<!DOCTYPE html>
<html lang="en">

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
                    <a href="index.php">Página inicial</a>
                    <a href="pesquisa.php">Pesquisa</a>
                </nav>
            </div>
        </div>
    </header>
    <div class="container-lg inserir">
        <h2>Inserir Novo Livro</h2>
        <?php if ($msg): ?>
            <div class="alert alert-info"><?= $msg ?></div>
        <?php endif; ?>
        <form action="inserir_livro.php" method="POST" enctype="multipart/form-data" class="mb-5 inserir">
            <input type="text" name="titulo" placeholder="Título" required class="form-control mb-3" />
            <input type="number" name="ano" placeholder="Ano" required min="1100" max="2099" step="1" class="form-control mb-3" />
            
            
            

             <label for="autor">Autor:</label>
             <select name="autor_id" id="autor" class="form-select mb-3" required>
             <option value="">Selecione um autor</option>
             <?php foreach($autores as $autor): ?>
                <option value="<?= $autor['id'] ?>"><?= $autor['nome'] ?></option>
               <?php endforeach; ?>
            </select>

            <label for="capa" class="form-label">Capa do Livro (imagem):</label>
            <input type="file" name="capa" id="capa" accept="image/*" required class="form-control mb-3" />
            <button type="submit" class="btn btn-secondary">Inserir Livro</button>
        </form>
        <div class="editar">
            <h2>Opções</h2>
            <a href="inserir_autor.php" class="btn btn-secondary">Inserir autor</a>
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

