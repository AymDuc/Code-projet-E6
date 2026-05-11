<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $adminUser = getenv('PLANNING_ADMIN_USER') ?: 'admin';
    $adminPass = getenv('PLANNING_ADMIN_PASS') ?: 'admin123';
    if ($user === $adminUser && $pass === $adminPass) {
        $_SESSION['logged_in'] = true;
        header('Location: planning.php');
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion Planning Hôtel Hinano</title>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="icon" type="image/png" sizes="64x64" href="favicon-64.png">
  <link rel="apple-touch-icon" href="apple-touch-icon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f6efe8;
      --paper:#fffaf5;
      --text:#27181a;
      --muted:#6f595c;
      --line:rgba(105,61,69,.14);
      --brand:#8f3b48;
      --brand-2:#b65c68;
      --shadow:0 24px 60px rgba(80,34,41,.14);
    }
    *{box-sizing:border-box}
    body{
      margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;
      font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:var(--text);
      background:
        radial-gradient(900px 520px at 0% 0%, rgba(182,92,104,.10), transparent 55%),
        radial-gradient(750px 420px at 100% 10%, rgba(143,59,72,.08), transparent 50%),
        linear-gradient(180deg, #f6efe8 0%, #f8f2ec 45%, #f4ece6 100%);
    }
    .login-shell{width:min(100%, 960px);display:grid;grid-template-columns:1.06fr .94fr;overflow:hidden;border-radius:28px;background:rgba(255,253,249,.84);border:1px solid var(--line);box-shadow:var(--shadow)}
    .login-side{padding:34px;background:linear-gradient(180deg, rgba(255,255,255,.72), rgba(248,238,236,.92))}
    .login-brand{display:flex;align-items:center;gap:14px;margin-bottom:20px}
    .login-brand img.emblem{width:64px;height:64px;object-fit:contain}
    .login-brand img.wordmark{height:54px;width:auto;max-width:100%;object-fit:contain;object-position:left center}
    .login-kicker{display:inline-block;padding:8px 12px;border-radius:999px;background:rgba(143,59,72,.08);color:var(--brand);font-size:12px;font-weight:700;letter-spacing:.3px;text-transform:uppercase}
    h1{margin:14px 0 10px;font-size:34px;line-height:1.05;color:#3b2024}
    p{margin:0 0 14px;color:var(--muted);line-height:1.6}
    .login-card{padding:34px;display:flex;align-items:center;justify-content:center;background:rgba(255,250,245,.68)}
    form{width:min(100%, 380px)}
    h2{margin:0 0 6px;color:#3b2024;font-size:28px}
    .sub{margin:0 0 18px;color:var(--muted)}
    label{display:block;margin:12px 0 6px;color:var(--muted);font-size:13px;font-weight:600}
    input{
      width:100%;padding:13px 14px;border-radius:14px;border:1px solid var(--line);background:rgba(255,255,255,.92);
      color:var(--text);outline:none;font-size:15px
    }
    input:focus{border-color:rgba(143,59,72,.45);box-shadow:0 0 0 4px rgba(143,59,72,.10)}
    button{
      width:100%;margin-top:16px;padding:13px 16px;border:none;border-radius:14px;cursor:pointer;
      background:linear-gradient(135deg,var(--brand),var(--brand-2));color:#fff;font-weight:800;font-size:15px;
      box-shadow:0 12px 26px rgba(143,59,72,.18)
    }
    .error{margin:10px 0 0;padding:10px 12px;border-radius:12px;background:rgba(187,63,86,.10);color:#8f2337;border:1px solid rgba(187,63,86,.18)}
    .back{display:inline-flex;margin-top:18px;color:var(--brand);font-weight:700;text-decoration:none}
    .login-list{margin:18px 0 0;padding-left:18px;color:var(--muted);line-height:1.7}
    @media (max-width: 860px){
      .login-shell{grid-template-columns:1fr}
      .login-side{padding:28px}
      .login-card{padding:28px}
      h1{font-size:30px}
    }
  </style>
</head>
<body>
  <div class="login-shell">
    <section class="login-side">
      <div class="login-brand">
        <img src="favicon.png" alt="Symbole Hôtel Hinano" class="emblem">
        <img src="logo-wordmark.png" alt="Hôtel Hinano" class="wordmark">
      </div>
      <span class="login-kicker">Espace pro</span>
      <h1>Connexion à la gestion du planning</h1>
      <p>Accès réservé à la gérance et aux personnes autorisées. Le design a été harmonisé avec le logo officiel de l’Hôtel Hinano, sans modifier la page du planning lui-même.</p>
      <ul class="login-list">
        <li>Accès rapide au planning interne</li>
        <li>Interface plus cohérente avec le site public</li>
        <li>Icône d’onglet ajoutée pour une identité plus propre</li>
      </ul>
      <a class="back" href="index.php">← Retour au site</a>
    </section>

    <section class="login-card">
      <form method="post">
        <h2>Connexion</h2>
        <p class="sub">Entre tes identifiants pour ouvrir le planning interne.</p>
        <?php if (!empty($error)): ?>
          <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <label for="username">Identifiant</label>
        <input id="username" type="text" name="username" placeholder="Identifiant" required>

        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" placeholder="Mot de passe" required>

        <button type="submit">Se connecter</button>
      </form>
    </section>
  </div>
</body>
</html>
