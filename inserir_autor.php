<?php
 
$conn = new mysqli('127.0.0.1', 'root', '', 'livros_db');
 
if ($conn->connect_error) {
    die('Erro na ligação: ' . $conn->connect_error);
}
 
 
$msg = '';
 

 
    $diretorio_fotos = 'uploads/pictures/';
 
    // Verificar se o diretório existe
    if (!is_dir($diretorio_fotos)) {
        mkdir($diretorio_fotos, 0755, true);
    }
 
    $imagem = $_FILES['fotos'];
    $fileName = basename($imagem['name']);
    $imagem_caminho = $diretorio_fotos . $fileName;
 
    // Validar se o ficheiro é uma imagem
    $check = getimagesize($imagem['tmp_name']);
    if ($check == false) {
        $msg = "O ficheiro enviado não é uma imagem valida";
    } else {
        if (move_uploaded_file($imagem['tmp_name'], $imagem_caminho)) {
            $sql = 'INSERT INTO atores (nome, nascimento, nacionalidade,foto) VALUES (?, ?, ?,?)';
            $query = mysqli_prepare($conn, $sql);
            if ($query) {
                mysqli_stmt_bind_param($query, 'sis', $nome, $nascimento,$nacionalidade, $imagem_caminho);
                if (mysqli_stmt_execute($query)) {
                    $msg = 'Autor inserido com sucesso!';
                } else {
                    $msg = 'Erro ao inserir ator: ' . mysqli_error($conn);
                }
                mysqli_stmt_close($query);
            } else {
                $msg = 'Erro na preparação da query: ' . mysqli_error($conn);
            }
        }
    }
 
// procurar autores
            $result = $conn->query("SELECT id, nome, nacionalidade, foto FROM autores ORDER BY nome");
            $autores = $result->fetch_all(MYSQLI_ASSOC);
 
mysqli_close($conn);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
    <header class="container-fluid">
        <div class="container-lg">
            <div class="row align-items-center">
                <h1 class="col-4">livros_db</h1>
                <nav class="col text-end">
                    <a href="index.php">Página inicial</a>
                    <a href="pesquisa.php">Pesquisa</a>
                </nav>
            </div>
        </div>
    </header>
    <div class="container-lg inserir">
        <h2>Inserir Novo Autor</h2>
        <?php if ($msg): ?>
            <div class="alert alert-info"><?= $msg ?></div>
        <?php endif; ?>
         <div class="autor container-lg">
        <div class="row align-items-center">
            <h2><?php echo htmlspecialchars($autor['nome']) ?></h2>
            <img src="<?php echo htmlspecialchars($autor['foto']) ?>"
                alt="" class="col-3">
            <div class="informacao col-8">
                <p><span class="rotulo ano">Nascimento:</span> <?php echo htmlspecialchars($autor['data_nascimento']) ?></p>
                <p><span class="rotulo genero">Nacionalidade:</span> <?php echo htmlspecialchars($autor['nacionalidade']) ?></p>
            </div>
        <form action="inserir_autor.php" method="POST" enctype="multipart/form-data" class="mb-5 inserir">
            <input type="text" name="nome" placeholder="Nome" required class="form-control mb-3" />
            <input type="date" name="nascimento" required class="form-control mb-3" />
            
            <input type="text" name="nacionalidade" placeholder="Nacionalidade" required class="form-control mb-3" />
            <label for="foto" class="form-label">Foto do autor (imagem):</label>
            <input type="file" name="foto" id="foto" accept="image/*" required class="form-control mb-3" />
            <button type="submit" class="btn btn-primary">Inserir Autor</button>
        </form>
        <div class="editar">
            <h2>Opções</h2>
            <a href="inserir_autor.php" class="btn btn-primary">Inserir Autor</a>
        </div>
    </div>
    <footer class="container-fluid text-center">
        <div class="container-lg">
            <p>&copy; 2025 IMDb2.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>

</html>
