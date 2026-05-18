<?php
session_start();

$isComplete = false;
$role = $_SESSION['role'] ?? null;
if (!$role) {
    header("Location: ../index.php");
    exit;
}

$profile = [];
$errors = [];
$user = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile — Job Portal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --bg: #0d0f14; --surface: #161923; --border: #252b38;
  --accent: #f0a500; --text: #e8eaf0; --muted: #7a8499;
  --danger: #e05050; --success: #2eb87e; --radius: 10px;
}
body { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--text); min-height:100vh;
  background-image:radial-gradient(ellipse 80% 50% at 50% -10%,rgba(240,165,0,.12),transparent); }
nav { display:flex; justify-content:space-between; align-items:center; padding:1rem 2rem;
  border-bottom:1px solid var(--border); background:rgba(13,15,20,.85); backdrop-filter:blur(12px); }
.logo { font-family:'Syne',sans-serif; font-weight:800; font-size:1.3rem; color:var(--accent); text-decoration:none; }
.logo span { color:var(--text); }
nav a { color:var(--muted); text-decoration:none; font-size:.875rem; padding:.4rem .8rem; border-radius:6px; transition:.2s; }
nav a:hover { color:var(--text); background:var(--border); }
.wrap { max-width:600px; margin:1.5rem auto; padding:0 1.25rem; }
.card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:2rem; box-shadow:0 8px 32px rgba(0,0,0,.45); margin-bottom:1.25rem; }
h1 { font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; margin-bottom:.3rem; }
h3 { font-family:'Syne',sans-serif; font-size:.95rem; color:var(--muted); margin-bottom:1rem; letter-spacing:.05em; }
.sub { color:var(--muted); font-size:.9rem; margin-bottom:1.5rem; }
.form-group { margin-bottom:1.15rem; }
label { display:block; font-size:.75rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:var(--muted); margin-bottom:.4rem; }
input[type=text], input[type=url], input[type=number], input[type=password], select, textarea {
  width:100%; background:var(--bg); border:1px solid var(--border); border-radius:var(--radius);
  color:var(--text); padding:.65rem .9rem; font-family:'DM Sans',sans-serif; font-size:.95rem; outline:none; transition:.2s; }
input:focus, select:focus, textarea:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(240,165,0,.15); }
input.is-error, select.is-error, textarea.is-error { border-color:var(--danger); }
textarea { resize:vertical; min-height:90px; }
select option { background:var(--surface); }
.err { color:var(--danger); font-size:.8rem; margin-top:.3rem; display:block; }
.file-zone { border:2px dashed var(--border); border-radius:var(--radius); padding:1.25rem; text-align:center;
  cursor:pointer; transition:.2s; position:relative; }
.file-zone:hover { border-color:var(--accent); background:rgba(240,165,0,.04); }
.file-zone input[type=file] { position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; }
.btn { display:block; width:100%; padding:.8rem; border:none; border-radius:var(--radius); cursor:pointer;
  font-family:'DM Sans',sans-serif; font-size:.95rem; font-weight:600; transition:.2s; text-align:center; }
.btn-primary { background:var(--accent); color:#000; }
.btn-primary:hover { background:#fbb830; transform:translateY(-1px); }
.btn-outline { background:transparent; color:var(--text); border:1px solid var(--border); }
.btn-outline:hover { border-color:var(--accent); color:var(--accent); }
.alert { border-radius:var(--radius); padding:.8rem 1rem; font-size:.875rem; margin-bottom:1rem; border-left:3px solid; }
.alert-danger  { background:rgba(224,80,80,.1);  border-color:var(--danger);  color:#f8a0a0; }
.alert-success { background:rgba(46,184,126,.1); border-color:var(--success); color:#70e0b0; }
.alert-warning { background:rgba(240,165,0,.1);  border-color:var(--accent);  color:#f8cc6e; }
.divider { height:1px; background:var(--border); margin:1.75rem 0; }
</style>
</head>
<body>
<nav>
  <a class="logo" href="#">Job<span>Portal</span></a>
  <div style="display:flex;gap:.5rem;align-items:center;">
    <span style="color:var(--muted);font-size:.875rem;">Hi, <?= htmlspecialchars($_SESSION['name'] ?? '') ?></span>
    <a href="../controllers/Logout.php">Log out</a>
  </div>
</nav>

<div class="wrap">

  <?php if (!$isComplete): ?>
  <div class="alert alert-warning">⚠️ <strong>Profile Incomplete</strong> — Fill in your details to unlock all features.</div>
  <?php endif; ?>

  <?php if (!empty($_GET['saved'])): ?>
  <div class="alert alert-success">✓ Profile saved successfully.</div>
  <?php endif; ?>

  <div class="card">
    <h1><?= $isComplete ? '✏️ Edit Profile' : '👋 Complete Your Profile' ?></h1>
    <p class="sub"><?= $role === 'employer' ? 'Tell candidates about your company.' : 'Help employers find you.' ?></p>

    <form method="POST" action="../controllers/ProfileController.php" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="save_profile" value="1">

      <?php if ($role === 'employer'): ?>

        <div class="form-group">
          <label for="company_name">Company Name</label>
          <input type="text" id="company_name" name="company_name"
            value="<?= htmlspecialchars($profile['company_name'] ?? $_POST['company_name'] ?? '') ?>"
            class="<?= !empty($errors['company_name']) ? 'is-error' : '' ?>"
            placeholder="Acme Corp">
          <?php if (!empty($errors['company_name'])): ?><span class="err"><?= htmlspecialchars($errors['company_name']) ?></span><?php endif; ?>
        </div>

        <div class="form-group">
          <label for="industry">Industry</label>
          <select id="industry" name="industry" class="<?= !empty($errors['industry']) ? 'is-error' : '' ?>">
            <option value="">Select industry…</option>
            <?php foreach (['Technology','Finance','Healthcare','Education','Marketing','Engineering','Design','Sales','Legal','Other'] as $ind): ?>
            <option value="<?= $ind ?>" <?= ($profile['industry'] ?? $_POST['industry'] ?? '') === $ind ? 'selected' : '' ?>><?= $ind ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (!empty($errors['industry'])): ?><span class="err"><?= htmlspecialchars($errors['industry']) ?></span><?php endif; ?>
        </div>

        <div class="form-group">
          <label for="description">Company Description</label>
          <textarea id="description" name="description" placeholder="What does your company do?"><?= htmlspecialchars($profile['description'] ?? $_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label for="website">Website <span style="color:var(--muted);text-transform:none;letter-spacing:0;">(optional)</span></label>
          <input type="url" id="website" name="website"
            value="<?= htmlspecialchars($profile['website'] ?? $_POST['website'] ?? '') ?>"
            class="<?= !empty($errors['website']) ? 'is-error' : '' ?>"
            placeholder="https://company.com">
          <?php if (!empty($errors['website'])): ?><span class="err"><?= htmlspecialchars($errors['website']) ?></span><?php endif; ?>
        </div>

        <div class="form-group">
          <label>Company Logo <span style="color:var(--muted);text-transform:none;letter-spacing:0;">(optional)</span></label>
          <?php if (!empty($user['file_path'])): ?>
            <div style="margin-bottom:.5rem;">
              <img src="../<?= htmlspecialchars($user['file_path']) ?>" alt="Logo" style="max-height:55px;border-radius:6px;border:1px solid var(--border);">
            </div>
          <?php endif; ?>
          <div class="file-zone">
            <input type="file" name="file" id="fileInput" accept="image/*">
            <strong>Click to upload</strong> new logo (JPG/PNG, max 2MB)
            <p id="fileName" style="color:var(--accent);font-size:.82rem;margin-top:.3rem;"></p>
          </div>
          <?php if (!empty($errors['file'])): ?><span class="err"><?= htmlspecialchars($errors['file']) ?></span><?php endif; ?>
        </div>

      <?php else: ?>

        <div class="form-group">
          <label for="headline">Professional Headline</label>
          <input type="text" id="headline" name="headline"
            value="<?= htmlspecialchars($profile['headline'] ?? $_POST['headline'] ?? '') ?>"
            class="<?= !empty($errors['headline']) ? 'is-error' : '' ?>"
            placeholder="e.g. Junior Full-Stack Developer">
          <?php if (!empty($errors['headline'])): ?><span class="err"><?= htmlspecialchars($errors['headline']) ?></span><?php endif; ?>
        </div>

        <div class="form-group">
          <label for="skills">Skills <span style="color:var(--muted);text-transform:none;letter-spacing:0;">(comma-separated)</span></label>
          <input type="text" id="skills" name="skills"
            value="<?= htmlspecialchars($profile['skills'] ?? $_POST['skills'] ?? '') ?>"
            class="<?= !empty($errors['skills']) ? 'is-error' : '' ?>"
            placeholder="PHP, MySQL, JavaScript">
          <?php if (!empty($errors['skills'])): ?><span class="err"><?= htmlspecialchars($errors['skills']) ?></span><?php endif; ?>
        </div>

        <div class="form-group">
          <label for="years_experience">Years of Experience</label>
          <input type="number" id="years_experience" name="years_experience" min="0" max="50"
            value="<?= htmlspecialchars((string)($profile['years_experience'] ?? $_POST['years_experience'] ?? 0)) ?>">
        </div>

        <div class="form-group">
          <label>Resume PDF <span style="color:var(--muted);text-transform:none;letter-spacing:0;">(optional, max 2MB)</span></label>
          <?php if (!empty($user['file_path'])): ?>
            <div style="margin-bottom:.5rem;">
              <a href="../<?= htmlspecialchars($user['file_path']) ?>" target="_blank" style="color:var(--accent);font-size:.85rem;">📄 View current resume</a>
            </div>
          <?php endif; ?>
          <div class="file-zone">
            <input type="file" name="file" id="fileInput" accept="application/pdf">
            <strong>Click to upload</strong> new resume (PDF only)
            <p id="fileName" style="color:var(--accent);font-size:.82rem;margin-top:.3rem;"></p>
          </div>
          <?php if (!empty($errors['file'])): ?><span class="err"><?= htmlspecialchars($errors['file']) ?></span><?php endif; ?>
        </div>

      <?php endif; ?>

      <button type="submit" class="btn btn-primary">Save Profile</button>
    </form>

    <div class="divider"></div>

    <!-- Password Change -->
    <h3>CHANGE PASSWORD</h3>
    <div class="form-group">
      <label>Current Password</label>
      <input type="password" id="cur_pw" placeholder="••••••••">
    </div>
    <div class="form-group">
      <label>New Password</label>
      <input type="password" id="new_pw" placeholder="••••••••">
    </div>
    <div class="form-group">
      <label>Confirm New Password</label>
      <input type="password" id="con_pw" placeholder="••••••••">
    </div>
    <div id="pwMsg" style="display:none;" class="alert"></div>
    <button class="btn btn-outline" id="changePwBtn">Update Password</button>
  </div>
</div>

<script>
document.getElementById('fileInput')?.addEventListener('change', function() {
  document.getElementById('fileName').textContent = this.files[0] ? '📎 ' + this.files[0].name : '';
});

document.getElementById('changePwBtn').addEventListener('click', async () => {
  const btn  = document.getElementById('changePwBtn');
  const msg  = document.getElementById('pwMsg');
  const body = new FormData();
  body.append('change_password', '1');
  body.append('current_password', document.getElementById('cur_pw').value);
  body.append('new_password',     document.getElementById('new_pw').value);
  body.append('confirm_password', document.getElementById('con_pw').value);

  btn.disabled = true; btn.textContent = 'Updating…';
  try {
    const res  = await fetch('../controllers/ProfileController.php', { method:'POST', body });
    const data = await res.json();
    msg.style.display = 'block';
    msg.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
    msg.textContent = data.message;
    if (data.success) {
      document.getElementById('cur_pw').value = '';
      document.getElementById('new_pw').value = '';
      document.getElementById('con_pw').value = '';
    }
  } catch(e) {
    msg.style.display = 'block';
    msg.className = 'alert alert-danger';
    msg.textContent = 'Network error. Please try again.';
  }
  btn.disabled = false; btn.textContent = 'Update Password';
});
</script>
</body>
</html>
