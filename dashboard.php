<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.html");
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Biblioteca — Inicio</title>
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
  <a href="dashboard.php" class="active"><i class="bi bi-house"></i> Inicio</a>
  <div class="section-label">Catálogo</div>
  <a href="autores.php"><i class="bi bi-person-lines-fill"></i> Autores</a>
  <a href="libros.php"><i class="bi bi-book"></i> Libros</a>
  <div class="section-label">Préstamos</div>
  <a href="prestamos.php"><i class="bi bi-bookmark-check"></i> Mis préstamos</a>
</aside>

<main>

  <div class="welcome-card">
    <div>
      <h2>Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?></h2>
      <p>¿Qué quieres hacer hoy?</p>
    </div>
    <i class="bi bi-book-half wc-icon"></i>
  </div>

  <?php
  require_once 'db.php';
  $db = conectarDB();
  $nAutores    = $db->query("SELECT COUNT(*) FROM autores")->fetchColumn();
  $nLibros     = $db->query("SELECT COUNT(*) FROM libros")->fetchColumn();
  $nDisponibles= $db->query("SELECT COUNT(*) FROM libros WHERE disponible=1")->fetchColumn();
  $nPrestamos  = $db->query("SELECT COUNT(*) FROM prestamos WHERE estado='activo'")->fetchColumn();
  ?>

  <div class="stat-grid">
    <div class="stat-card">
      <i class="bi bi-person-lines-fill ico"></i>
      <div class="num"><?= $nAutores ?></div>
      <div class="lbl">Autores</div>
    </div>
    <div class="stat-card">
      <i class="bi bi-book ico"></i>
      <div class="num"><?= $nLibros ?></div>
      <div class="lbl">Libros</div>
    </div>
    <div class="stat-card">
      <i class="bi bi-check2-circle ico"></i>
      <div class="num"><?= $nDisponibles ?></div>
      <div class="lbl">Disponibles</div>
    </div>
    <div class="stat-card">
      <i class="bi bi-bookmark-check ico"></i>
      <div class="num"><?= $nPrestamos ?></div>
      <div class="lbl">Préstamos activos</div>
    </div>
  </div>

  <div class="quick-grid">
    <a class="quick-link" href="autores.php">
      <i class="bi bi-person-plus"></i>
      <div>
        <div class="ql-title">Agregar<br>autor</div>
        <div class="ql-sub">Catálogo</div>
      </div>
    </a>
    <a class="quick-link" href="libros.php">
      <i class="bi bi-journal-plus"></i>
      <div>
        <div class="ql-title">Agregar<br>libro</div>
        <div class="ql-sub">Catálogo</div>
      </div>
    </a>
    <a class="quick-link" href="prestamos.php">
      <i class="bi bi-bookmark-plus"></i>
      <div>
        <div class="ql-title">Pedir<br>préstamo</div>
        <div class="ql-sub">Préstamos</div>
      </div>
    </a>
  </div>

</main>

</body>
</html>
