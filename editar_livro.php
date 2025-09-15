<?php
// LIGAR BASE DE DADOS 
$conexao = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conexao) {
    die('Erro na ligação: ' . mysqli_connect_error());
}


// Variáveis para mensagens e dados do livro
$mensagem = "";

// $livros = [
//     'id' => '',
//     'titulo' => '',
//     'ano' => '',
//     'id_autor' => '',
//     'capa' => ''
// ];

// --- BUSCAR livro SE O ID FOR FORNECIDO NA URL ---

echo $_GET['id'];
if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $resultado = mysqli_query($conexao, "SELECT * FROM livros WHERE id = $id");
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $livros = mysqli_fetch_assoc($resultado);
    }
}

// --- PROCESSAR FORMULÁRIO SE FOI SUBMETIDO ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($livros['id'])) {
    $id = (int)$livros['id'];
    $titulo = $_POST['titulo'];
    $ano = (int)$_POST['ano'];

    // capa inicial: o que já está guardado na base de dados
    $caminho_capa = $livros['capa'] ?? '';

    // --- VERIFICAR SE UM NOVO capa FOI ENVIADO ---
    if (!empty($_FILES['capa']['name'])) {
        $pasta_destino = __DIR__ . "/uploads/capas/";
        $pasta_destino_bd = "uploads/capas/";

        if (!is_dir($pasta_destino)) {
            mkdir($pasta_destino, 0755, true);
        }

        $ficheiro = $_FILES['capa'];
        $nome_ficheiro = basename($ficheiro['name']);
        $caminho_completo = $pasta_destino . $nome_ficheiro;
        $caminho_bd = $pasta_destino_bd . $nome_ficheiro;

        if (getimagesize($ficheiro['tmp_name']) !== false) {
            if (move_uploaded_file($ficheiro['tmp_name'], $caminho_completo)) {
                $caminho_capa = $caminho_bd;
            } else {
                $mensagem = "Erro: não consegui mover o ficheiro para $caminho_completo";
            }
        } else {
            $mensagem = "Erro: o ficheiro enviado não é uma imagem válida.";
        }
    }

    // --- ATUALIZAR livro NA BASE DE DADOS COM PREPARED STATEMENT ---
    if (!$mensagem) {
        $sql = "UPDATE livros 
                SET titulo = ?, ano = ?, capa = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conexao, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sisi",
                $titulo,
                $ano,
                $caminho_capa,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                $mensagem = "livro atualizado com sucesso!";
                $resultado = mysqli_query($conexao, "SELECT * FROM livros WHERE id = $id");
                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    $livros = mysqli_fetch_assoc($resultado);
                }
            } else {
                $mensagem = "Erro ao atualizar livro: " . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        } else {
            $mensagem = "Erro na preparação do SQL: " . mysqli_error($conexao);
        }
    }
}

// procurar autores
$res = mysqli_query($conexao, "SELECT id, nome FROM autores ORDER BY nome");
$autores = mysqli_fetch_all($res, MYSQLI_ASSOC);

// Procurar todos os títulos para o dropdown
$resTitulos = mysqli_query($conexao, "SELECT id, titulo FROM livros ORDER BY titulo");
$titulos = mysqli_fetch_all($resTitulos, MYSQLI_ASSOC);

mysqli_close($conexao);

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h2>Editar livros </h2>

        <?php if ($mensagem): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($mensagem) ?></div>
        <?php endif ?>

        <form action="editar_livro.php?id=<?= $livros['id'] ?>" method="POST" enctype="multipart/form-data" class="inserir">

            <label for="titulo">Título:</label>
            <select name="titulo" id="titulo" class="form-select mb-3" required>
                <option value="">Selecione um título</option>
                <?php foreach ($titulos as $t): ?>
                    <option value="<?= htmlspecialchars($t['titulo']) ?>"
                        <?= ($livros['titulo'] == $t['titulo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>






            <input type="number" name="ano" placeholder="Ano" required min="1100" max="2099" step="1"
                class="form-control mb-3" value="<?php echo htmlspecialchars($livros['ano']) ?>" />









            <label for="capa" class="form-label">Capa do livro (imagem):</label>
            <input type="file" name="capa" id="capa" accept="image/*" class="form-control mb-3" />

            <?php if (!empty($livros['capa'])): ?>
                <img src="<?php echo htmlspecialchars($livros['capa']) ?>" alt="Capa do livro" class="foto">
            <?php endif; ?>

            <button type="submit" class="btn btn-secondary">Editar Livro</button>
            <br><br><br>

        </form>
    </div>

    <footer class="container-fluid text-center">
        <div class="container-lg">
            <p>&copy; 2025 Website de livros</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>