<!-- ADICIONAR NA PAGINA LIVROS -->

<?php

$conn = mysqli_connect('127.0.0.1', 'root', '', 'livros_db');
if (!$conn) {
    die('Erro na ligação: ' . mysqli_connect_error());
}

// Fetch all authors for the dropdown
$sql_todos_autores = "SELECT id, nome FROM autores ORDER BY nome ASC";
$resultado_todos_autores = mysqli_query($conn, $sql_todos_autores);

// Handle adding an actor to this movie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_author'])) {
    $id_autor = (int) $_POST['id_autor'];

    if ($id_autor > 0) {
        $sql_insert = "INSERT INTO autores_livros (id_livro, id_autor) VALUES ($id, $id_autor)";
        mysqli_query($conn, $sql_insert);
    }
}

?>

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