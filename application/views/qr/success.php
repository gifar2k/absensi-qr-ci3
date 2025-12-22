<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Absensi Berhasil</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --bg1:#0b1220;
      --bg2:#0f1b33;
      --card: rgba(255,255,255,.08);
      --border: rgba(255,255,255,.14);
      --text: rgba(255,255,255,.90);
      --muted: rgba(255,255,255,.65);
    }
    body{
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      min-height: 100vh;
      color: var(--text);
      background:
        radial-gradient(1200px 600px at 15% 10%, rgba(34,211,238,.16), transparent 60%),
        radial-gradient(1200px 600px at 85% 15%, rgba(167,139,250,.16), transparent 55%),
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
    .mono{ font-variant-numeric: tabular-nums; }
    .badge-soft{
      background: rgba(34,197,94,.16);
      border: 1px solid rgba(34,197,94,.35);
      color: #d7ffe4;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-7">
        <div class="glass p-4 p-md-5 text-center">
          <div class="d-flex justify-content-center mb-2">
            <span class="badge badge-soft px-3 py-2 rounded-pill">ABSENSI BERHASIL ✅</span>
          </div>

          <h3 class="mb-1"><?= htmlspecialchars($user->nama) ?></h3>
          <div class="muted mono mb-3" id="liveClock">--</div>

          <div class="row g-3 text-start mt-2">
            <div class="col-12 col-md-6">
              <div class="muted small">Status</div>
              <div class="fs-5"><b><?= htmlspecialchars($status) ?></b></div>
            </div>
            <div class="col-12 col-md-6">
              <div class="muted small">Waktu tersimpan (server)</div>
              <div class="fs-5 mono"><b><?= htmlspecialchars($data['tanggal'].' '.$data['jam']) ?> WIB</b></div>
            </div>

            <div class="col-12">
              <hr class="border-opacity-25 my-2">
              <div class="muted small">Lokasi</div>
              <?php if(!empty($data['lat']) && !empty($data['lng'])): ?>
                <div class="mono"><b><?= htmlspecialchars($data['lat']) ?>, <?= htmlspecialchars($data['lng']) ?></b></div>
              <?php else: ?>
                <div class="muted">Tidak dikirim</div>
              <?php endif; ?>
            </div>
          </div>

          <div class="mt-4 muted small">
            Silakan tutup halaman ini.
          </div>
        </div>

        <div class="text-center mt-3 muted small">
          © Absensi QR CI3 • Premium UI
        </div>
      </div>
    </div>
  </div>

<script>
(function(){
  const el = document.getElementById('liveClock');
  const days = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
  const months = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
  const pad = (n)=>String(n).padStart(2,'0');

  function tick(){
    const d = new Date();
    el.textContent = `${days[d.getDay()]}, ${pad(d.getDate())} ${months[d.getMonth()]} ${d.getFullYear()} • ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())} WIB`;
  }
  tick();
  setInterval(tick, 1000);
})();
</script>
</body>
</html>
