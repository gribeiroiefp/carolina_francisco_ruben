<?php

$conexao = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');

if (!$conexao) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

$mensagem = "";
$autor = null;

// --- BUSCAR AUTOR SE O ID FOR FORNECIDO NA URL --- 
if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $resultado = mysqli_query($conexao, "SELECT * FROM autores WHERE id = $id");
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $autor = mysqli_fetch_assoc($resultado);
    }
} // --- PROCESSAR FORMULÁRIO SE FOI SUBMETIDO --- 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($autor['id'])) {
    // Receber dados do formulário 
    $id = (int)$autor['id'];
    $nome = $_POST['nome'];
    $data_nascimento = $_POST['data_nascimento'];
    // Aceitar valor vazio 
    $nacionalidade = $_POST['nacionalidade'];
    // Foto inicial: a que já está guardada na base de dados 
    $caminho_foto = $autor['foto'] ?? '';
    // --- VERIFICAR SE UMA NOVA FOTO FOI ENVIADA --- 
    if (!empty($_FILES['foto']['name'])) {
        // Caminho absoluto para guardar no servidor 
        $pasta_destino = __DIR__ . "/uploads/fotos/";
        // Caminho relativo a guardar na base de dados 
        $pasta_destino_bd = "uploads/fotos/";
        // Criar a pasta se não existir 
        if (!is_dir($pasta_destino)) {
            mkdir($pasta_destino, 0755, true);
        }
        // Dados do ficheiro carregado 
        $ficheiro = $_FILES['foto'];
        $nome_ficheiro = basename($ficheiro['name']);
        $caminho_completo = $pasta_destino . $nome_ficheiro;
        $caminho_bd = $pasta_destino_bd . $nome_ficheiro;
        // Verificar se é uma imagem válida 
        if (getimagesize($ficheiro['tmp_name']) !== false) {
            // Mover a imagem para a pasta definitiva 
            if (move_uploaded_file($ficheiro['tmp_name'], $caminho_completo)) {
                $caminho_foto = $caminho_bd;
                // Atualizar caminho para a BD 
            } else {
                $mensagem = "Erro: não consegui mover o ficheiro para $caminho_completo";
            }
        } else {
            $mensagem = "Erro: o ficheiro enviado não é uma imagem válida.";
        }
    }
    // --- ATUALIZAR Autor NA BASE DE DADOS COM PREPARED STATEMENT --- 
    if (!$mensagem) {
        // Criar SQL com placeholders 
        $sql = "UPDATE autores SET nome = ?, data_nascimento = ?, nacionalidade = ?, foto = ? WHERE id = ?";
        // Preparar a instrução 
        $stmt = mysqli_prepare($conexao, $sql);
        if ($stmt) {
            // Associar os parâmetros aos placeholders 
            // s = string, i = inteiro 
            mysqli_stmt_bind_param($stmt, "ssssi", $nome, $data_nascimento, $nacionalidade, $caminho_foto, $id);
            // Executar a instrução 
            if (mysqli_stmt_execute($stmt)) {
                $mensagem = "Autor atualizado com sucesso!";
                // Recarregar os dados do Autor atualizado 
                $resultado = mysqli_query($conexao, "SELECT * FROM autores WHERE id = $id");
                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    $autor = mysqli_fetch_assoc($resultado);
                }
            } else {
                $mensagem = "Erro ao atualizar autor: " . mysqli_stmt_error($stmt);
            }
            // Fechar o statement 
            mysqli_stmt_close($stmt);
        } else {
            $mensagem = "Erro na preparação do SQL: " . mysqli_error($conexao);
        }
    }
}
// Fechar ligação à base de dados 
mysqli_close($conexao);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Autor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <header class="container-fluid">
        <div class="container-lg">
            <div class="row align-items-center">
                <h1 class="col-4">Website de livros</h1>
                <nav class="col text-end"> <a href="index.php">Página inicial</a> <a href="pesquisa.php">Pesquisa</a> </nav>
            </div>
        </div>
    </header>
    <div class="container-lg inserir">
        <h2>Editar Autor</h2> <!-- Mostrar mensagem de sucesso/erro --> <?php if ($mensagem): ?> <div class="alert alert-info"><?php echo htmlspecialchars($mensagem) ?></div> <?php endif ?> <!-- Formulário de edição -->
        <form action="editar_autor.php?id=<?php echo htmlspecialchars($autor['id']) ?>" method="POST" enctype="multipart/form-data" class="mb-5 inserir">
            <input type="text" name="nome" placeholder="Nome" required class="form-control mb-3" value="<?php echo htmlspecialchars($autor['nome']) ?>" />
            <input type="date" name="data_nascimento" required class="form-control mb-3" value="<?php echo htmlspecialchars($autor['data_nascimento']) ?>" />
            <input type="text" name="nacionalidade" placeholder="Nacionalidade" required class="form-control mb-3" value="<?php echo htmlspecialchars($autor['nacionalidade']) ?>" />
            <label for="foto" class="form-label">Foto do autor (imagem):</label>
            <input type="file" name="foto" id="foto" accept="image/*" class="form-control mb-3" /> 
            <!-- Mostrar foto atual --> 
            <?php if (!empty($autor['foto'])): ?>
                <img src="<?php echo htmlspecialchars($autor['foto']) ?>" alt="Foto do autor" class="foto"> 
            <?php endif ?>
            <button type="submit" class="btn btn-secondary">Editar Autor</button>
        </form>
        <!-- <div class="editar">
            <h2>Opções</h2> <a href="inserir_filme.php" class="btn btn-primary">Inserir Filme</a> <a href="inserir_autor.php" class="btn btn-primary">Inserir Autor</a>
        </div> -->
    </div>
    <footer class="container-fluid text-center">
        <div class="container-lg">
            <p>&copy;2025 Website de livros</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>