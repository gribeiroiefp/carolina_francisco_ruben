<?php



$conn = new mysqli('127.0.0.1', 'root', '', 'livros_db');

if ($conn->connect_error) {
    die('Erro na ligação: ' . $conn->connect_error);
}

$mensagem = "";
// $autores = [
//     'id' => '',
//     'nome' => '',
//     'data_nascimento' => '',
//     'nacionalidade' => '',
//     'foto' => ''
// ];
 

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $resultado = mysqli_query($conexao, "SELECT * FROM autores WHERE id = $id");
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $autores = mysqli_fetch_assoc($resultado);
    }
}
 

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($autores['id'])) {
    $id = (int)$autores['id'];
    $nome = $_POST['nome'];
    $data_nascimento = (int)$_POST['ano'];
    
 
    $caminho_pictures = $autores['foto'] ?? '';
 
    if (!empty($_FILES['foto']['name'])) {
        $pasta_destino = __DIR__ . "/uploads/fotos/";
        $pasta_destino_bd = "uploads/fotos/";
 
        if (!is_dir($pasta_destino)) {
            mkdir($pasta_destino, 0755, true);
        }
 
        $ficheiro = $_FILES['foto'];
        $nome_ficheiro = basename($ficheiro['name']);
        $caminho_completo = $pasta_destino . $nome_ficheiro;
        $caminho_bd = $pasta_destino_bd . $nome_ficheiro;
 
        if (getimagesize($ficheiro['tmp_name']) !== false) {
            if (move_uploaded_file($ficheiro['tmp_name'], $caminho_completo)) {
                $caminho_pictures = $caminho_bd;
            } else {
                $mensagem = "Erro: não consegui mover o ficheiro para $caminho_completo";
            }
        } else {
            $mensagem = "Erro: o ficheiro enviado não é uma imagem válida.";
        }
    }
 
    
    if (!$mensagem) {
        $sql = "UPDATE autores
                SET nome = ?, nascimento = ?, nacionalidade = ? foto =?
                WHERE id = ?";
 
        $stmt = mysqli_prepare($conexao, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sisi",
                $id,
                $nome,
                $data_nascimento,
                $caminho_fotos,
                $id
            );
 
            if (mysqli_stmt_execute($stmt)) {
                $mensagem = "autor atualizado com sucesso!";
                $resultado = mysqli_query($conexao, "SELECT * FROM autores WHERE id = $id");
                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    $autores = mysqli_fetch_assoc($resultado);
                }
            } else {
                $mensagem = "Erro ao atualizar autor: " . mysqli_stmt_error($stmt);
            }
 
            mysqli_stmt_close($stmt);
        } else {
            $mensagem = "Erro na preparação do SQL: " . mysqli_error($conexao);
        }
    }
}


 


mysqli_close($conexao);
 
?>
 
<!DOCTYPE html>
<html lang="pt">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar autores</title>
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
        <h2>Editar autores </h2>
 
        <?php if ($mensagem): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($mensagem) ?></div>
        <?php endif ?>
 
        
        <label for="titulo">Nome:</label>
        <select name="nome" id="nome" class="form-select mb-3" required>
            <option value="">Selecione um autor </option>
            <?php foreach ($nome as $t): ?>
                <option value="<?= htmlspecialchars($t['titulo']) ?>"
                    <?= ($autores['nome'] == $t['nome']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
 
 
 
 
        
 
        <input type int="date" name="ano" placeholder="Ano" required min="1100" max="2099" step="1"
            class="form-control mb-3" value="<?php echo htmlspecialchars($autores['ano']) ?>" />
 
 
 
 
        <label for="autor">Autor:</label>
        <select name="autor_id" id="autor" class="form-select mb-3" required>
            <option value="">Selecione um autor</option>
            <?php foreach ($autores as $autor): ?>
                <input type int="date" name="ano" placeholder="Ano" required min="1100" max="2099" step="1"
            class="form-control mb-3" value="<?php echo htmlspecialchars($autores['ano']) ?>" />
             <option value="<?php echo $autor['id'] ?>"
                    <?php echo ($livros['id_autor'] == $autor['id']) ? 'selected' : '' ?>>
                    <?php echo htmlspecialchars($autor['nome']) ?>
                    <?php echo htmlspecialchars($autor['nacionalidade']) ?>
 

 

                </option>
            <?php endforeach; ?>
        </select>
 
 
 
 
 
 
 
        <label for="foto" class="form-label"> Foto do autor (imagem):</label>
        <input type="file" name="pictures" id="imagem" accept="image/*" class="form-control mb-3" />
 
        <?php if (!empty($livros['capa'])): ?>
            <img src="<?php echo htmlspecialchars($livros['foto']) ?>" alt="imagem do autor" class="foto">
        <?php endif; ?>
 
        <button type="submit" class="btn btn-primary">Editar autor</button>
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
 >
