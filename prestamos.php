<?php
session_start();
if (!isset($_SESSION['id'])) { header("Location: index.html"); exit(); }
require_once 'db.php';
$db = conectarDB();
$msg = '';
$usuario_id = $_SESSION['id'];

// Pedir libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['libro_id'])) {
    $libro_id = (int) $_POST['libro_id'];
    $libro = $db->prepare("SELECT disponible FROM libros WHERE id=?");
    $libro->execute([$libro_id]);
    $lib = $libro->fetch();
    if ($lib && $lib['disponible']) {
        $db->prepare("INSERT INTO prestamos (usuario_id, libro_id) VALUES (?, ?)")->execute([$usuario_id, $libro_id]);
        $db->prepare("UPDATE libros SET disponible=0 WHERE id=?")->execute([$libro_id]);
        $msg = 'success:Libro solicitado correctamente.';
    } else {
        $msg = 'error:El libro no está disponible.';
    }
}

// Devolver libro
if (isset($_GET['devolver'])) {
    $prestamo_id = (int) $_GET['devolver'];
    $p = $db->prepare("SELECT libro_id FROM prestamos WHERE id=? AND usuario_id=?");
    $p->execute([$prestamo_id, $usuario_id]);
    $pr = $p->fetch();
    if ($pr) {
        $db->prepare("UPDATE prestamos SET estado='devuelto', fecha_devolucion=CURDATE() WHERE id=?")->execute([$prestamo_id]);
        $db->prepare("UPDATE libros SET disponible=1 WHERE id=?")->execute([$pr['libro_id']]);
        $msg = 'success:Libro devuelto. ¡Gracias!';
    }
}

$disponibles = $db->query("
    SELECT l.id, l.titulo, a.nombre AS autor
    FROM libros l
    JOIN autores a ON l.autor_id = a.id
    WHERE l.disponible = 1
    ORDER BY l.titulo ASC
")->fetchAll();

$misprestamos = $db->prepare("
    SELECT p.id, l.titulo, a.nombre AS autor, p.fecha_prestamo, p.fecha_devolucion, p.estado
    FROM prestamos p
    JOIN libros l ON p.libro_id = l.id
    JOIN autores a ON l.autor_id = a.id
    WHERE p.usuario_id = ?
    ORDER BY p.fecha_prestamo DESC
");
$misprestamos->execute([$usuario_id]);
$misprestamos = $misprestamos->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Préstamos — Biblioteca</title>
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
  <a href="libros.php"><i class="bi bi-book"></i> Libros</a>
  <div class="section-label">Préstamos</div>
  <a href="prestamos.php" class="active"><i class="bi bi-bookmark-check"></i> Mis préstamos</a>
</aside>

<main>

  <div class="page-header">
    <div>
      <h1 class="page-title">Présta<em>mos</em></h1>
      <p class="page-subtitle">Solicita y gestiona tus libros</p>
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
    <h2 class="card-title">Pedir un libro</h2>
    <?php if (empty($disponibles)): ?>
      <div class="empty-state">
        <i class="bi bi-bookmark-x"></i>
        <p>No hay libros disponibles en este momento.</p>
      </div>
    <?php else: ?>
    <form method="POST">
      <div class="field-group">
        <label class="bib-label">Selecciona un libro disponible</label>
        <select class="bib-select" name="libro_id" required>
          <option value="">— Elige un libro —</option>
          <?php foreach ($disponibles as $l): ?>
            <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['titulo']) ?> — <?= htmlspecialchars($l['autor']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="bib-btn" type="submit"><i class="bi bi-bookmark-plus"></i> Pedir préstamo</button>
    </form>
    <?php endif; ?>
  </div>

  <div class="bib-card">
    <h2 class="card-title">Mis préstamos</h2>
    <?php if (empty($misprestamos)): ?>
      <div class="empty-state">
        <i class="bi bi-bookmark"></i>
        <p>No has pedido ningún libro todavía.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Libro</th>
              <th>Autor</th>
              <th>Fecha</th>
              <th>Estado</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($misprestamos as $p): ?>
            <tr>
              <td style="font-weight:500;"><?= htmlspecialchars($p['titulo']) ?></td>
              <td style="color:var(--ink-soft);"><?= htmlspecialchars($p['autor']) ?></td>
              <td style="color:var(--ink-soft);font-size:12px;"><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>
              <td>
                <span class="badge <?= $p['estado'] === 'activo' ? 'badge-activo' : 'badge-devuelto' ?>">
                  <i class="bi <?= $p['estado'] === 'activo' ? 'bi-clock' : 'bi-check-circle' ?>"></i>
                  <?= ucfirst($p['estado']) ?>
                </span>
              </td>
              <td>
                <?php if ($p['estado'] === 'activo'): ?>
                  <a href="prestamos.php?devolver=<?= $p['id'] ?>" onclick="return confirm('¿Devolver este libro?')">
                    <button class="btn-action"><i class="bi bi-arrow-return-left"></i> Devolver</button>
                  </a>
                <?php else: ?>
                  <span style="color:var(--parch-dk);font-size:12px;"><?= $p['fecha_devolucion'] ?></span>
                <?php endif; ?>
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
