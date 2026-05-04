<?php
session_start();
if (!isset($_SESSION['id'])) { header("Location: index.html"); exit(); }
require_once 'db.php';
$db = conectarDB();
$msg = '';

// Agregar libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $titulo   = trim($_POST['titulo']);
    $autor_id = (int) $_POST['autor_id'];
    if ($titulo && $autor_id) {
        $db->prepare("INSERT INTO libros (titulo, autor_id) VALUES (?, ?)")->execute([$titulo, $autor_id]);
        $msg = 'success:Libro agregado correctamente.';
    }
}

// Eliminar libro
if (isset($_GET['delete'])) {
    try {
        $db->prepare("DELETE FROM libros WHERE id=?")->execute([$_GET['delete']]);
        $msg = 'success:Libro eliminado.';
    } catch (Exception $e) {
        $msg = 'error:No se puede eliminar, tiene préstamos asociados.';
    }
}

$autores = $db->query("SELECT * FROM autores ORDER BY nombre ASC")->fetchAll();
$libros  = $db->query("
    SELECT l.id, l.titulo, l.disponible, l.created_at, a.nombre AS autor
    FROM libros l
    JOIN autores a ON l.autor_id = a.id
    ORDER BY l.titulo ASC
")->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Libros — Biblioteca</title>
  <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
  <link rel="stylesheet" href="biblioteca.css">
</head>
<body>

<header>
  <span class="header-logo">
    <i class="bi bi-book-half"></i>
    Biblio<em>teca</em>
  </span>
  <nav class="header-nav">
    <a href="dashboard.php"><i class="bi bi-house"></i> Inicio</a>
    <a href="logout.php" class="danger"><i class="bi bi-box-arrow-right"></i> Salir</a>
  </nav>
</header>

<aside>
  <div class="section-label">Menú</div>
  <a href="dashboard.php"><i class="bi bi-house"></i> Inicio</a>
  <div class="section-label">Catálogo</div>
  <a href="autores.php"><i class="bi bi-person-lines-fill"></i> Autores</a>
  <a href="libros.php" class="active"><i class="bi bi-book"></i> Libros</a>
  <div class="section-label">Préstamos</div>
  <a href="prestamos.php"><i class="bi bi-bookmark-check"></i> Mis préstamos</a>
</aside>

<main>

  <div class="page-header">
    <div>
      <h1 class="page-title">Li<em>bros</em></h1>
      <p class="page-subtitle"><?= count($libros) ?> libro<?= count($libros) !== 1 ? 's' : '' ?> en el catálogo</p>
    </div>
  </div>

  <?php if ($msg): ?>
    <?php [$tipo, $texto] = explode(':', $msg, 2); ?>
    <div class="alert <?= $tipo === 'success' ? 'alert-ok' : 'alert-err' ?>">
      <i class="bi <?= $tipo === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle' ?>"></i>
      <?= $texto ?>
    </div>
  <?php endif; ?>

  <div class="bib-card">
    <h2 class="card-title">Agregar libro</h2>
    <?php if (empty($autores)): ?>
      <div class="empty-state">
        <i class="bi bi-person-lines-fill"></i>
        <p>Primero debes <a href="autores.php" style="color:var(--gold);font-weight:500;">agregar autores</a> antes de registrar libros.</p>
      </div>
    <?php else: ?>
    <form method="POST">
      <div class="field-group">
        <label class="bib-label">Título del libro</label>
        <input class="bib-input" type="text" name="titulo" placeholder="Ej: Cien años de soledad" required>
      </div>
      <div class="field-group" style="margin-top:1rem;">
        <label class="bib-label">Autor</label>
        <select class="bib-select" name="autor_id" required>
          <option value="">— Selecciona un autor —</option>
          <?php foreach ($autores as $a): ?>
            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="bib-btn" type="submit"><i class="bi bi-plus-lg"></i> Agregar libro</button>
    </form>
    <?php endif; ?>
  </div>

  <div class="bib-card">
    <h2 class="card-title">Lista de libros</h2>
    <?php if (empty($libros)): ?>
      <div class="empty-state">
        <i class="bi bi-book"></i>
        <p>Aún no hay libros registrados.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Título</th>
              <th>Autor</th>
              <th>Estado</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($libros as $l): ?>
            <tr>
              <td style="color:var(--parch-dk);font-size:12px;"><?= $l['id'] ?></td>
              <td style="font-weight:500;"><?= htmlspecialchars($l['titulo']) ?></td>
              <td style="color:var(--ink-soft);"><?= htmlspecialchars($l['autor']) ?></td>
              <td>
                <span class="badge <?= $l['disponible'] ? 'badge-disp' : 'badge-no' ?>">
                  <i class="bi <?= $l['disponible'] ? 'bi-check-circle' : 'bi-clock' ?>"></i>
                  <?= $l['disponible'] ? 'Disponible' : 'Prestado' ?>
                </span>
              </td>
              <td>
                <a href="libros.php?delete=<?= $l['id'] ?>" onclick="return confirm('¿Eliminar este libro?')">
                  <button class="btn-action danger"><i class="bi bi-trash3"></i> Eliminar</button>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</main>

</body>
</html>
