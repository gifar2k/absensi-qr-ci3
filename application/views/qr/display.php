<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?></title>

  <!-- Bootstrap (CDN dulu biar cepat) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Inter (premium) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Theme -->
  <link href="<?= base_url('assets/css/theme.css') ?>" rel="stylesheet">

  <style>
    :root{
      --radius: 22px;
    }
    body{
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      min-height: 100vh;
      background:
        radial-gradient(1200px 600px at 20% 10%, rgba(99,102,241,.20), transparent 60%),
        radial-gradient(900px 500px at 80% 30%, rgba(16,185,129,.18), transparent 60%),
        radial-gradient(900px 500px at 50% 90%, rgba(59,130,246,.14), transparent 60%),
        #0b1220;
      color: #e5e7eb;
      overflow-x: hidden;
    }
    .glass{
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.10);
      box-shadow: 0 20px 60px rgba(0,0,0,.35);
      backdrop-filter: blur(12px);
      border-radius: var(--radius);
    }
    .muted{ color: rgba(229,231,235,.72); }
    .qr-wrap{
      width: min(520px, 85vw);
      aspect-ratio: 1/1;
      border-radius: 26px;
      padding: 18px;
      background: rgba(255,255,255,.07);
      border: 1px solid rgba(255,255,255,.12);
      box-shadow: 0 30px 80px rgba(0,0,0,.45);
    }
    .qr-img{
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 18px;
      background: #fff;
    }
    .badge-soft{
      border: 1px solid rgba(255,255,255,.15);
      background: rgba(255,255,255,.06);
      color: #e5e7eb;
      border-radius: 999px;
      padding: .45rem .8rem;
      font-weight: 600;
      letter-spacing: .2px;
    }
    .topbar{
      position: sticky;
      top: 0;
      padding-top: 14px;
      padding-bottom: 14px;
      background: linear-gradient(to bottom, rgba(11,18,32,.92), rgba(11,18,32,.30));
      backdrop-filter: blur(10px);
      z-index: 10;
    }
    .brand-dot{
      width: 10px; height: 10px; border-radius: 999px;
      background: #22c55e;
      box-shadow: 0 0 0 6px rgba(34,197,94,.15);
      display:inline-block;
      margin-right: 10px;
      transform: translateY(1px);
    }
    @media (max-width: 576px){
      .qr-wrap{ width: 92vw; padding: 14px; }
    }
  </style>
</head>

<body>
  <div class="topbar">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="d-flex align-items-center">
            <span class="brand-dot"></span>
            <div>
              <div class="fw-bold" style="font-size: 1.05rem;"><?= htmlspecialchars($office['office_name'] ?? 'iLog Computer') ?></div>
              <div class="small muted">Scan QR untuk absensi • Real-time</div>
            </div>
          </div>
        </div>
        <div class="text-end">
          <div class="badge-soft small">
            Refresh dalam <span id="countdown"><?= (int)$refresh_seconds ?></span>s
          </div>
          <div class="small muted mt-1" id="serverTime">--:--:--</div>
        </div>
      </div>
    </div>
  </div>

  <main class="container py-4 py-md-5">
    <div class="row g-4 align-items-center justify-content-center">
      <div class="col-12 col-lg-7">
        <div class="glass p-4 p-md-5">
          <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
              <div class="fw-bold" style="font-size: clamp(1.2rem, 2.6vw, 1.8rem); line-height: 1.15;">
                QR Absensi Pegawai
              </div>
              <div class="muted mt-2">
                1) Scan QR • 2) Isi email • 3) Pilih <b>MASUK</b>/<b>PULANG</b> • 4) Aktifkan lokasi
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-center my-4 my-md-5">
            <div class="qr-wrap">
              <img
                id="qrImage"
                class="qr-img"
                src="<?= site_url('qr/png') ?>"
                alt="QR Absensi"
                loading="eager"
              >
            </div>
          </div>

          <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <div class="small muted">
              Jika QR tidak berubah, refresh halaman monitor.
            </div>
            <button class="btn btn-sm btn-outline-light" id="btnRefresh" type="button">
              Refresh QR
            </button>
          </div>
        </div>
      </div>

      
    </div>
  </main>

  <script>
    (function(){
      const refreshSeconds = <?= (int)$refresh_seconds ?>;
      const countdownEl = document.getElementById('countdown');
      const qrImg = document.getElementById('qrImage');
      const btn = document.getElementById('btnRefresh');
      const serverTimeEl = document.getElementById('serverTime');

      let remaining = refreshSeconds;

      function pad(n){ return (n < 10 ? '0' : '') + n; }

      function setServerTime(){
        // just display client time (close enough for UI); real validation is server-side
        const d = new Date();
        serverTimeEl.textContent = pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
      }

      function reloadQr(){
        // cache-bust real-time
        qrImg.src = "<?= site_url('qr/png') ?>?v=" + Date.now();
        remaining = refreshSeconds;
        countdownEl.textContent = remaining;
      }

      btn.addEventListener('click', reloadQr);

      setInterval(function(){
        remaining--;
        if (remaining <= 0){
          reloadQr();
        } else {
          countdownEl.textContent = remaining;
        }
        setServerTime();
      }, 1000);

      // first time show
      setServerTime();
    })();
  </script>
</body>
</html>
