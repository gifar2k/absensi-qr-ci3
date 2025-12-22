<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($title ?? 'Admin') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{
      --bg: #0B1020;
      --text: #E9ECF1;
      --muted: rgba(233,236,241,.70);
      --stroke: rgba(255,255,255,.10);
      --glass: rgba(255,255,255,.05);
      --glass-2: rgba(255,255,255,.06);
    }

    body{
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background: var(--bg);
      color: var(--text);
    }

    /* === LAYOUT WRAP === */
    .admin-wrap{ min-height: 100vh; }

    /* === PREMIUM SIDEBAR === */
    .admin-sidebar{
      width: 260px;
      min-height: 100vh;
      background: linear-gradient(180deg, rgba(18,18,22,.95), rgba(12,12,15,.95));
      border-right: 1px solid rgba(255,255,255,.06);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      position: sticky;
      top: 0;
      color: #e9ecf1;
    }

    .sidebar-brand{
      display:flex; align-items:center; gap:12px;
    }
    .brand-icon{
      width:42px; height:42px; border-radius:12px;
      background: linear-gradient(135deg,#4f8cff,#7aa7ff);
      display:flex; align-items:center; justify-content:center;
      font-weight:700; color:#fff;
      box-shadow: 0 14px 30px rgba(0,0,0,.35);
    }
    .brand-title{ font-weight:700; line-height: 1.1; }
    .brand-sub{ font-size:12px; color: rgba(233,236,241,.65); }

    .sidebar-user{ display:flex; gap:12px; align-items:center; }
    .sidebar-user .avatar{
      width:40px; height:40px; border-radius:50%;
      background: rgba(255,255,255,.08);
      display:flex; align-items:center; justify-content:center;
      font-weight:700;
      border: 1px solid rgba(255,255,255,.08);
    }
    .muted{ color: rgba(233,236,241,.6); }

    .admin-sidebar .nav-section{
      margin:14px 8px 6px;
      font-size:11px;
      letter-spacing:.08em;
      color: rgba(233,236,241,.45);
      user-select:none;
    }

    .admin-sidebar .nav-link{
      color: rgba(233,236,241,.82);
      border-radius: 12px;
      padding: 10px 12px;
      display:flex;
      align-items:center;
      gap:10px;
      transition: .15s ease;
    }
    .admin-sidebar .nav-link i{
      font-size: 1.05rem;
      opacity: .9;
    }

    .admin-sidebar .nav-link:hover{
      background: rgba(255,255,255,.06);
      color:#fff;
    }

    .admin-sidebar .nav-link.active{
      background: linear-gradient(135deg, rgba(79,140,255,.25), rgba(122,167,255,.15));
      color:#fff;
      box-shadow: inset 0 0 0 1px rgba(79,140,255,.35);
    }

    .admin-sidebar .nav-divider{
      height:1px;
      margin:12px 0;
      background: rgba(255,255,255,.08);
    }

    .admin-sidebar .nav-link.logout{
      color:#ff6b6b;
    }
    .admin-sidebar .nav-link.logout:hover{
      background: rgba(255,107,107,.15);
      color:#fff;
    }

    /* === MAIN === */
    .admin-main{
      background: radial-gradient(1200px 600px at top right, rgba(79,140,255,.08), transparent), #0e0f13;
      min-height: 100vh;
    }

    .content{ padding: 22px; }

    /* === CARD GLASS (fix kontras) === */
    .card-glass{
      background: var(--glass);
      border: 1px solid var(--stroke);
      border-radius: 18px;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      color: var(--text);
      box-shadow: 0 18px 50px rgba(0,0,0,.35);
    }

    .card-glass .small,
    .card-glass .text-muted,
    .card-glass p,
    .card-glass .muted{
      color: var(--muted) !important;
    }

    .card-glass .fw-semibold,
    .card-glass .fw-bold,
    .card-glass h1,
    .card-glass h2,
    .card-glass h3,
    .card-glass h4,
    .card-glass h5,
    .card-glass h6{
      color: var(--text);
    }
  </style>
</head>

<body>
<div class="d-flex admin-wrap">
