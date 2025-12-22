<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin • Absensi QR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg1:#0b1220; --bg2:#0f1b33;
      --card: rgba(255,255,255,.08);
      --border: rgba(255,255,255,.14);
      --text: rgba(255,255,255,.90);
      --muted: rgba(255,255,255,.65);
    }
    body{
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      min-height:100vh; color:var(--text);
      background:
        radial-gradient(1200px 600px at 15% 10%, rgba(125,211,252,.20), transparent 60%),
        radial-gradient(1200px 600px at 85% 10%, rgba(167,139,250,.18), transparent 55%),
        linear-gradient(180deg, var(--bg1), var(--bg2));
    }
    .glass{
      background: var(--card);
      border: 1px solid var(--border);
      box-shadow: 0 20px 50px rgba(0,0,0,.35);
      backdrop-filter: blur(14px);
      border-radius: 24px;
    }
    .muted{ color: var(--muted); }
    .form-control{
      border-radius: 14px;
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.14);
      color: var(--text);
    }
    .form-control:focus{
      box-shadow:none;
      border-color: rgba(99,102,241,.55);
    }
    .btn-premium{ border-radius: 14px; padding: 12px 14px; }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-5">
        <div class="glass p-4 p-md-5">
          <h3 class="mb-1">Login Admin</h3>
          <div class="muted mb-4">Masuk untuk kelola pegawai & rekap absensi.</div>

          <?php if($this->session->flashdata('err')): ?>
            <div class="alert alert-danger rounded-4"><?= $this->session->flashdata('err') ?></div>
          <?php endif; ?>

          <form method="post" action="<?= site_url('auth/do_login') ?>">
            <div class="mb-3">
              <label class="form-label muted">Email</label>
              <input type="email" name="email" class="form-control" autocomplete="username" required>
            </div>
            <div class="mb-3">
              <label class="form-label muted">Password</label>
              <input type="password" name="password" class="form-control" autocomplete="current-password" required>
            </div>
            <button class="btn btn-primary w-100 btn-premium">Masuk</button>
          </form>

          <div class="text-center mt-4 muted small">
            © Absensi QR CI3 • Admin Panel
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
