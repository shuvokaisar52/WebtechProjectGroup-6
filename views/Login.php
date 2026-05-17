<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Job Portal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --bg: #0d0f14; --surface: #161923; --border: #252b38;
  --accent: #f0a500; --text: #e8eaf0; --muted: #7a8499;
  --danger: #e05050; --success: #2eb87e; --radius: 10px;
}
body { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; flex-direction:column;
  background-image:radial-gradient(ellipse 80% 50% at 50% -10%,rgba(240,165,0,.12),transparent); }
nav { display:flex; justify-content:space-between; align-items:center; padding:1rem 2rem;
  border-bottom:1px solid var(--border); background:rgba(13,15,20,.85); backdrop-filter:blur(12px); }
.logo { font-family:'Syne',sans-serif; font-weight:800; font-size:1.3rem; color:var(--accent); text-decoration:none; }
.logo span { color:var(--text); }
nav a { color:var(--muted); text-decoration:none; font-size:.875rem; padding:.4rem .8rem; border-radius:6px; transition:.2s; }
nav a:hover { color:var(--text); background:var(--border); }
.wrap { max-width:440px; margin:2rem auto; padding:0 1.25rem; width:100%; }
.card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:2rem; box-shadow:0 8px 32px rgba(0,0,0,.45); }
h1 { font-family:'Syne',sans-serif; font-weight:800; font-size:1.8rem; margin-bottom:.3rem; }
.sub { color:var(--muted); font-size:.9rem; margin-bottom:1.5rem; }
.form-group { margin-bottom:1.15rem; }
label { display:block; font-size:.75rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:var(--muted); margin-bottom:.4rem; }
input { width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius);
  color:var(--text); padding:.65rem .9rem; font-family:'DM Sans',sans-serif; font-size:.95rem; outline:none; transition:.2s; }
input:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(240,165,0,.15); }
input.is-error { border-color:var(--danger); }
.err { color:var(--danger); font-size:.8rem; margin-top:.3rem; display:block; }
.btn { display:block; width:100%; padding:.8rem; border:none; border-radius:var(--radius); cursor:pointer;
  font-family:'DM Sans',sans-serif; font-size:.95rem; font-weight:600; transition:.2s; text-align:center; text-decoration:none; }
.btn-primary { background:var(--accent); color:#000; }
.btn-primary:hover { background:#fbb830; transform:translateY(-1px); }
.btn-outline { background:transparent; color:var(--text); border:1px solid var(--border); margin-top:.75rem; }
.btn-outline:hover { border-color:var(--accent); color:var(--accent); }
.alert { border-radius:var(--radius); padding:.8rem 1rem; font-size:.875rem; margin-bottom:1rem; border-left:3px solid; }
.alert-danger  { background:rgba(224,80,80,.1);  border-color:var(--danger);  color:#f8a0a0; }
.alert-success { background:rgba(46,184,126,.1); border-color:var(--success); color:#70e0b0; }
.divider { display:flex; align-items:center; gap:1rem; color:var(--muted); font-size:.8rem; margin:1.25rem 0; }
.divider::before, .divider::after { content:''; flex:1; height:1px; background:var(--border); }
</style>
</head>
<body>
<nav>
  <a class="logo" href="#">Job<span>Portal</span></a>
  <a href="Registration.php">Sign up</a>
</nav>

<div class="wrap">
  <div class="card">
    <h1>Welcome back</h1>
    <p class="sub">Log in to your account.</p>

    <?php if (!empty($_GET['registered'])): ?>
      <div class="alert alert-success">Account created! Please log in.</div>
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form method="POST" action="../controllers/LoginController.php" novalidate>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          class="<?= !empty($errors['email']) ? 'is-error' : '' ?>"
          placeholder="jane@example.com" autofocus>
        <?php if (!empty($errors['email'])): ?><span class="err"><?= htmlspecialchars($errors['email']) ?></span><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password"
          class="<?= !empty($errors['password']) ? 'is-error' : '' ?>"
          placeholder="••••••••">
        <?php if (!empty($errors['password'])): ?><span class="err"><?= htmlspecialchars($errors['password']) ?></span><?php endif; ?>
      </div>

      <button type="submit" class="btn btn-primary">Log In</button>
    </form>

    <div class="divider">no account yet?</div>
    <a href="Registration.php" class="btn btn-outline">Create account</a>
  </div>
</div>
</body>
</html>
