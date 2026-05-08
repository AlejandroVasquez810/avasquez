<?php
session_start();

if(isset($_COOKIE["id_usuario"])) {
  $_SESSION['id_usuario'] = $_COOKIE["id_usuario"];
    header("Location: dashboard.php");
    exit();
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biblioteca — Iniciar sesión</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
      :root {
        --ink:       #1a1209;
        --ink-mid:   #3d2b10;
        --ink-soft:  #7a5c38;
        --gold:      #c8922a;
        --gold-lt:   #e8c97a;
        --parch:     #f7f0e3;
        --parch-dk:  #ede0c8;
        --cream:     #fdfaf4;
        --red-acc:   #8b2020;
        --shadow:    rgba(26,18,9,0.12);
      }

      * { box-sizing: border-box; margin: 0; padding: 0; }

      body {
        min-height: 100vh;
        background: var(--parch);
        font-family: 'DM Sans', sans-serif;
        display: flex;
        align-items: stretch;
        overflow: hidden;
      }

      /* ── Left panel ─────────────────────────────────────── */
      .panel-left {
        flex: 1;
        background: var(--ink);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 3rem;
        position: relative;
        overflow: hidden;
      }

      .panel-left::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
          repeating-linear-gradient(0deg, transparent, transparent 39px, rgba(200,146,42,0.07) 40px),
          repeating-linear-gradient(90deg, transparent, transparent 39px, rgba(200,146,42,0.07) 40px);
      }

      .panel-left::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 30% 60%, rgba(200,146,42,0.12) 0%, transparent 60%);
      }

      .panel-brand {
        position: relative;
        z-index: 1;
        text-align: center;
      }

      .panel-brand .icon-wrap {
        width: 72px;
        height: 72px;
        border: 1px solid rgba(200,146,42,0.4);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
      }

      .panel-brand .icon-wrap i {
        font-size: 32px;
        color: var(--gold);
      }

      .panel-brand h1 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 52px;
        font-weight: 300;
        color: var(--cream);
        line-height: 1.1;
        letter-spacing: -0.5px;
      }

      .panel-brand h1 em {
        font-style: italic;
        color: var(--gold-lt);
      }

      .panel-brand p {
        margin-top: 1rem;
        font-size: 13px;
        color: rgba(255,255,255,0.35);
        font-weight: 300;
        letter-spacing: 2px;
        text-transform: uppercase;
      }

      .panel-quote {
        position: relative;
        z-index: 1;
        margin-top: 3rem;
        max-width: 280px;
        border-left: 2px solid var(--gold);
        padding-left: 1rem;
      }

      .panel-quote p {
        font-family: 'Cormorant Garamond', serif;
        font-style: italic;
        font-size: 17px;
        color: rgba(255,255,255,0.5);
        line-height: 1.6;
      }

      /* ── Right panel ─────────────────────────────────────── */
      .panel-right {
        width: 460px;
        background: var(--cream);
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 3rem 3.5rem;
        border-left: 1px solid var(--parch-dk);
        position: relative;
      }

      .panel-right::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--gold), var(--gold-lt), var(--gold));
      }

      .form-heading {
        font-family: 'Cormorant Garamond', serif;
        font-size: 36px;
        font-weight: 400;
        color: var(--ink);
        margin-bottom: 4px;
      }

      .form-sub {
        font-size: 13px;
        color: var(--ink-soft);
        font-weight: 300;
        margin-bottom: 2.5rem;
      }

      .field-group {
        margin-bottom: 1.4rem;
      }

      .field-label {
        display: block;
        font-size: 10px;
        font-weight: 500;
        color: var(--ink-soft);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 7px;
      }

      .field-input {
        width: 100%;
        background: var(--parch);
        border: 1px solid var(--parch-dk);
        border-radius: 6px;
        padding: 12px 16px;
        font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        color: var(--ink);
        outline: none;
        transition: border-color 0.2s, background 0.2s;
      }

      .field-input::placeholder { color: var(--parch-dk); }

      .field-input:focus {
        border-color: var(--gold);
        background: #fffef9;
      }

      .remember-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1.8rem;
        margin-top: -0.4rem;
      }

      .remember-row input[type="checkbox"] {
        width: 15px; height: 15px;
        accent-color: var(--gold);
        cursor: pointer;
      }

      .remember-row label {
        font-size: 13px;
        color: var(--ink-soft);
        cursor: pointer;
      }

      .btn-submit {
        width: 100%;
        background: var(--ink);
        color: var(--gold-lt);
        border: none;
        border-radius: 6px;
        padding: 14px;
        font-family: 'DM Sans', sans-serif;
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        position: relative;
        overflow: hidden;
      }

      .btn-submit::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(200,146,42,0.15) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.3s;
      }

      .btn-submit:hover { background: var(--ink-mid); }
      .btn-submit:hover::after { opacity: 1; }
      .btn-submit:active { transform: scale(0.995); }

      .form-footer {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 13px;
        color: var(--ink-soft);
      }

      .form-footer a {
        color: var(--ink-mid);
        font-weight: 500;
        text-decoration: none;
        border-bottom: 1px solid var(--gold);
        padding-bottom: 1px;
        transition: color 0.2s;
      }

      .form-footer a:hover { color: var(--gold); }

      .divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 1.5rem 0;
      }

      .divider::before, .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--parch-dk);
      }

      .divider span {
        font-size: 11px;
        color: var(--parch-dk);
        letter-spacing: 1px;
      }

      .bib-error {
        background: #fff5f5;
        border: 1px solid #f5c0c0;
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 13px;
        color: var(--red-acc);
        margin-bottom: 1.4rem;
        text-align: center;
      }

      @media (max-width: 768px) {
        body { flex-direction: column; overflow: auto; }
        .panel-left { flex: none; padding: 2.5rem 2rem; min-height: 220px; }
        .panel-brand h1 { font-size: 38px; }
        .panel-quote { display: none; }
        .panel-right { width: 100%; padding: 2.5rem 1.5rem; }
      }
    </style>
  </head>
  <body>

    <div class="panel-left">
      <div class="panel-brand">
        <div class="icon-wrap">
          <i class="bi bi-book-half"></i>
        </div>
        <h1>Biblio<br><em>teca</em></h1>
        <p>Sistema de Gestión</p>
      </div>
      <div class="panel-quote">
        <p>Un lector vive mil vidas antes de morir. El que no lee vive solo una.</p>
      </div>
    </div>

    <div class="panel-right">
      <h2 class="form-heading">Iniciar sesión</h2>
      <p class="form-sub">Accede a tu cuenta de lector</p>

      <form method="POST" action="login.php">

        <div class="field-group">
          <label class="field-label" for="email">Correo electrónico</label>
          <input class="field-input" type="email" id="email" name="email"
            placeholder="tu@correo.com"
            value="<?= isset($_COOKIE['recordar_email']) ? htmlspecialchars($_COOKIE['recordar_email']) : '' ?>"
            required>
        </div>

        <div class="field-group">
          <label class="field-label" for="pwd">Contraseña</label>
          <input class="field-input" type="password" id="pwd" name="pwd" placeholder="••••••••" required>
        </div>

        <div class="remember-row">
          <input type="checkbox" id="recordar" name="recordar" value="1"
            <?= isset($_COOKIE['recordar_email']) ? 'checked' : '' ?>>
          <label for="recordar">Recórdame por 30 días</label>
        </div>

        <button class="btn-submit" type="submit">Entrar a la biblioteca</button>
      </form>

      <div class="divider"><span>· · ·</span></div>

      <p class="form-footer">
        ¿No tienes cuenta? <a href="registro.html">Crear cuenta</a>
      </p>
    </div>

  </body>
</html>
