<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title) ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

 <style>
  :root{
    --glass: rgba(255,255,255,.08);
    --stroke: rgba(255,255,255,.14);
    --text-main: #E9ECF1;
    --text-muted: rgba(233,236,241,.70);
    --text-label: rgba(233,236,241,.80);
  }

  body{
    font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    min-height: 100vh;
    background:
      radial-gradient(900px 500px at 10% 10%, rgba(99,102,241,.30), transparent 60%),
      radial-gradient(900px 500px at 90% 20%, rgba(16,185,129,.22), transparent 60%),
      radial-gradient(900px 500px at 50% 90%, rgba(236,72,153,.18), transparent 60%),
      #070A12;
    color: var(--text-main);
  }

  /* === CARD GLASS === */
  .card-glass{
    background: var(--glass);
    border: 1px solid var(--stroke);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-radius: 18px;
    box-shadow: 0 20px 60px rgba(0,0,0,.45);
    color: var(--text-main);
  }

  /* === TEXT === */
  .card-glass h4{
    color: var(--text-main);
    font-weight: 600;
    letter-spacing: .2px;
  }

  .card-glass label{
    color: var(--text-label);
    font-weight: 500;
    margin-bottom: 4px;
  }

  .muted{
    color: var(--text-muted);
  }

  /* === INPUT === */
  .form-control{
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.14);
    color: var(--text-main);
    border-radius: 12px;
  }

  .form-control::placeholder{
    color: rgba(233,236,241,.45);
  }

  .form-control:focus{
    background: rgba(255,255,255,.10);
    border-color: rgba(99,102,241,.65);
    box-shadow: 0 0 0 .25rem rgba(99,102,241,.20);
    color: var(--text-main);
  }

  /* === BUTTON === */
  .btn-primary{
    border: 0;
    border-radius: 14px;
    background: linear-gradient(
      135deg,
      rgba(99,102,241,1),
      rgba(236,72,153,1)
    );
    color: #fff;
    font-weight: 600;
    box-shadow: 0 12px 28px rgba(99,102,241,.25);
    transition: transform .15s ease, box-shadow .15s ease;
  }

  .btn-primary:hover{
    transform: translateY(-1px);
    box-shadow: 0 16px 34px rgba(99,102,241,.35);
  }

  .btn-primary:active{
    transform: translateY(0);
    box-shadow: 0 10px 22px rgba(99,102,241,.25);
  }

  /* === BADGE === */
  .badge{
    background: rgba(255,255,255,.10) !important;
    color: var(--text-main);
    border: 1px solid rgba(255,255,255,.20);
    backdrop-filter: blur(6px);
  }

  /* === ALERT === */
  .alert-danger{
    background: rgba(220,53,69,.18);
    border: 1px solid rgba(220,53,69,.35);
    color: #ffd7db;
  }
</style>

</head>

<body class="d-flex align-items-center">
  <main class="container py-4">
    <div class="row justify-content-center">
      <div class="col-12 col-md-7 col-lg-5">
        <div class="card card-glass p-4 p-md-5">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
              <h4 class="mb-1">Admin Panel</h4>
          
            </div>
            <div class="badge rounded-pill text-bg-dark border" style="border-color:rgba(255,255,255,.18)!important">
              QR Absensi
            </div>
          </div>

          <?php if (!empty($err)): ?>
            <div class="alert alert-danger"><?= html_escape($err) ?></div>
          <?php endif; ?>

          <form method="post" action="<?= site_url('admin/login') ?>" autocomplete="off">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" placeholder="admin@domain.com" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button class="btn btn-primary w-100 py-2 fw-semibold">Masuk</button>

      
          </form>
        </div>

        <div class="text-center small muted mt-3">
          © <?= date('Y') ?> iLog Computer — Admin Area
        </div>
      </div>
    </div>
  </main>
</body>
</html>
