<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — Job Portal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --bg: #0d0f14; --surface: #161923; --border: #252b38;
  --accent: #f0a500; --text: #e8eaf0; --muted: #7a8499;
  --danger: #e05050; --success: #2eb87e; --radius: 10px;
}
body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh;
  display: flex; flex-direction: column;
  background-image: radial-gradient(ellipse 80% 50% at 50% -10%, rgba(240,165,0,.12), transparent); }
nav { display:flex; justify-content:space-between; align-items:center; padding:1rem 2rem;
  border-bottom:1px solid var(--border); background:rgba(13,15,20,.85); backdrop-filter:blur(12px); }
.logo { font-family:'Syne',sans-serif; font-weight:800; font-size:1.3rem; color:var(--accent); text-decoration:none; }
.logo span { color:var(--text); }
nav a { color:var(--muted); text-decoration:none; font-size:.875rem; padding:.4rem .8rem; border-radius:6px; transition:.2s; }
nav a:hover { color:var(--text); background:var(--border); }
.wrap { max-width:480px; margin:2rem auto; padding:0 1.25rem; width:100%; }
.card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:2rem; box-shadow:0 8px 32px rgba(0,0,0,.45); }
h1 { font-family:'Syne',sans-serif; font-weight:800; font-size:1.8rem; margin-bottom:.3rem; }
.sub { color:var(--muted); font-size:.9rem; margin-bottom:1.5rem; }
.form-group { margin-bottom:1.15rem; }
label { display:block; font-size:.75rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:var(--muted); margin-bottom:.4rem; }
input[type=text], input[type=email], input[type=password], select {
  width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius);
  color:var(--text); padding:.65rem .9rem; font-family:'DM Sans',sans-serif; font-size:.95rem; outline:none; transition:.2s; }
input:focus, select:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(240,165,0,.15); }
input.is-error { border-color:var(--danger); }
.err { color:var(--danger); font-size:.8rem; margin-top:.3rem; display:block; }
.role-group { display:flex; gap:.75rem; }
.role-group input[type=radio] { display:none; }
.role-group label { flex:1; text-align:center; padding:.75rem; border:2px solid var(--border); border-radius:var(--radius);
  cursor:pointer; font-size:.9rem; font-weight:500; text-transform:none; letter-spacing:0; color:var(--muted); transition:.2s; }
.role-group input[type=radio]:checked + label { border-color:var(--accent); color:var(--accent); background:rgba(240,165,0,.08); }
.file-zone { border:2px dashed var(--border); border-radius:var(--radius); padding:1.25rem; text-align:center;
  cursor:pointer; transition:.2s; position:relative; }
.file-zone:hover { border-color:var(--accent); background:rgba(240,165,0,.04); }
.file-zone input[type=file] { position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; }
.file-zone p { color:var(--muted); font-size:.82rem; margin-top:.25rem; }
.btn { display:block; width:100%; padding:.8rem; border:none; border-radius:var(--radius); cursor:pointer;
  font-family:'DM Sans',sans-serif; font-size:.95rem; font-weight:600; transition:.2s; text-align:center; text-decoration:none; }
.btn-primary { background:var(--accent); color:#000; }
.btn-primary:hover { background:#fbb830; transform:translateY(-1px); }
.btn-outline { background:transparent; color:var(--text); border:1px solid var(--border); margin-top:.75rem; }
.btn-outline:hover { border-color:var(--accent); color:var(--accent); }
.alert { border-radius:var(--radius); padding:.8rem 1rem; font-size:.875rem; margin-bottom:1rem; border-left:3px solid; }
.alert-danger { background:rgba(224,80,80,.1); border-color:var(--danger); color:#f8a0a0; }
.divider { display:flex; align-items:center; gap:1rem; color:var(--muted); font-size:.8rem; margin:1.25rem 0; }
.divider::before, .divider::after { content:''; flex:1; height:1px; background:var(--border); }
#fileGroup { display:none; }
#fileName { color:var(--accent); font-size:.82rem; margin-top:.3rem; }
</style>
</head>
<body>
<nav>
  <a class="logo" href="../index.php">Job<span>Portal</span></a>
  <div>
    <a href="../index.php">Home</a>
    <a href="Login.php">Log in</a>
  </div>
</nav>

<div class="wrap">
  <div class="card">
    <h1>Create account</h1>
    <p class="sub">Join as an employer or job seeker.</p>

    <?php if (!empty($errors['general'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form method="POST" action="../controllers/RegistrationController.php" enctype="multipart/form-data" novalidate>

      <div class="form-group">
        <label>I am a…</label>
        <div class="role-group">
          <input type="radio" name="role" id="role_employer" value="employer"
            <?= (($_POST['role'] ?? '') === 'employer') ? 'checked' : '' ?>>
          <label for="role_employer">🏢 Employer</label>
          <input type="radio" name="role" id="role_seeker" value="seeker"
            <?= (($_POST['role'] ?? '') === 'seeker') ? 'checked' : '' ?>>
          <label for="role_seeker">🔍 Job Seeker</label>
        </div>
        <?php if (!empty($errors['role'])): ?><span class="err"><?= htmlspecialchars($errors['role']) ?></span><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name"
          value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
          class="<?= !empty($errors['name']) ? 'is-error' : '' ?>"
          placeholder="Jane Smith">
        <?php if (!empty($errors['name'])): ?><span class="err"><?= htmlspecialchars($errors['name']) ?></span><?php endif; ?>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          class="<?= !empty($errors['email']) ? 'is-error' : '' ?>"
          placeholder="jane@example.com">
        <span class="err" id="email-error"><?= htmlspecialchars($errors['email'] ?? '') ?></span>
      </div>

      <div class="form-group">
        <label for="password">Password <span style="color:var(--muted);text-transform:none;letter-spacing:0;">(min 8 chars)</span></label>
        <input type="password" id="password" name="password"
          class="<?= !empty($errors['password']) ? 'is-error' : '' ?>"
          placeholder="••••••••">
        <?php if (!empty($errors['password'])): ?><span class="err"><?= htmlspecialchars($errors['password']) ?></span><?php endif; ?>
      </div>

      <div class="form-group" id="fileGroup">
        <label id="fileLabel">Upload</label>
        <div class="file-zone">
          <input type="file" name="file" id="fileInput">
          <strong>Click to upload</strong> or drag & drop
          <p id="fileHint"></p>
          <p id="fileName"></p>
        </div>
        <?php if (!empty($errors['file'])): ?><span class="err"><?= htmlspecialchars($errors['file']) ?></span><?php endif; ?>
      </div>

      <button type="submit" class="btn btn-primary">Create Account</button>
    </form>

    <div class="divider">already have an account?</div>
    <a href="Login.php" class="btn btn-outline">Log in</a>
  </div>
</div>

<script src="../controllers/js/CheckEmail.js"></script>
<script>
const employerRadio = document.getElementById('role_employer');
const seekerRadio   = document.getElementById('role_seeker');
const fileGroup     = document.getElementById('fileGroup');
const fileLabel     = document.getElementById('fileLabel');
const fileHint      = document.getElementById('fileHint');
const fileInput     = document.getElementById('fileInput');
const fileName      = document.getElementById('fileName');

function updateFileUI(role) {
  fileGroup.style.display = role ? 'block' : 'none';
  if (role === 'employer') {
    fileLabel.textContent = 'Company Logo (optional)';
    fileHint.textContent  = 'JPG, PNG, GIF, WEBP — max 2 MB';
    fileInput.accept      = 'image/*';
  } else {
    fileLabel.textContent = 'Resume PDF (optional)';
    fileHint.textContent  = 'PDF only — max 2 MB';
    fileInput.accept      = 'application/pdf';
  }
}

[employerRadio, seekerRadio].forEach(r => r.addEventListener('change', () => updateFileUI(r.value)));
const checked = document.querySelector('input[name=role]:checked');
if (checked) updateFileUI(checked.value);

fileInput.addEventListener('change', () => {
  fileName.textContent = fileInput.files[0] ? '📎 ' + fileInput.files[0].name : '';
});
</script>
</body>
</html>
