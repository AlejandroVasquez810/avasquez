<?php
session_start();
if (!isset($_SESSION['id'])) { header("Location: index.html"); exit(); }
require_once 'db.php';
$db = conectarDB();
$msg = '';

// Agregar autor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);
    if ($nombre) {
        $db->prepare("INSERT INTO autores (nombre) VALUES (?)")->execute([$nombre]);
        $msg = 'success:Autor agregado correctamente.';
    }
}

// Eliminar autor
if (isset($_GET['delete'])) {
    try {
        $db->prepare("DELETE FROM autores WHERE id=?")->execute([$_GET['delete']]);
        $msg = 'success:Autor eliminado.';
    } catch (Exception $e) {
        $msg = 'error:No se puede eliminar, tiene libros asociados.';
    }
}

$autores = $db->query("SELECT * FROM autores ORDER BY nombre ASC")->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Autores — Biblioteca</title>
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
  <a href="autores.php" class="active"><i class="bi bi-person-lines-fill"></i> Autores</a>
  <a href="libros.php"><i class="bi bi-book"></i> Libros</a>
  <div class="section-label">Préstamos</div>
  <a href="prestamos.php"><i class="bi bi-bookmark-check"></i> Mis préstamos</a>
</aside>

<main>

  <div class="page-header">
    <div>
      <h1 class="page-title">Auto<em>res</em></h1>
      <p class="page-subtitle"><?= count($autores) ?> autor<?= count($autores) !== 1 ? 'es' : '' ?> registrado<?= count($autores) !== 1 ? 's' : '' ?></p>
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
    <h2 class="card-title">Agregar autor</h2>
    <form method="POST">
      <div class="field-group">
        <label class="bib-label">Nombre del autor</label>
        <input class="bib-input" type="text" name="nombre" placeholder="Ej: Gabriel García Márquez" required>
      </div>
      <button class="bib-btn" type="submit"><i class="bi bi-plus-lg"></i> Agregar autor</button>
    </form>
  </div>

  <div class="bib-card">
    <h2 class="card-title">Lista de autores</h2>
    <?php if (empty($autores)): ?>
      <div class="empty-state">
        <i class="bi bi-person-lines-fill"></i>
        <p>Aún no hay autores registrados.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th>Registrado</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($autores as $a): ?>
            <tr>
              <td style="color:var(--parch-dk);font-size:12px;"><?= $a['id'] ?></td>
              <td style="font-weight:500;"><?= htmlspecialchars($a['nombre']) ?></td>
              <td style="color:var(--ink-soft);font-size:12px;"><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
              <td>
                <a href="autores.php?delete=<?= $a['id'] ?>" onclick="return confirm('¿Eliminar este autor?')">
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
