<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>QR Absensi</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="icon" href="data:,">

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
    .badge-soft{
      background: rgba(59,130,246,.18);
      border: 1px solid rgba(59,130,246,.35);
      color: #cfe5ff;
    }
    .qr-wrap{
      background: rgba(255,255,255,.92);
      border-radius: 20px;
      padding: 18px;
      display: inline-flex;
      box-shadow: 0 18px 40px rgba(0,0,0,.25);
    }
    .btn-premium{
      border-radius: 14px;
      padding: 12px 14px;
    }
    .small-muted{ color: var(--muted); font-size: .92rem; }
    a { color: #9dd5ff; text-decoration: none; }
    a:hover{ text-decoration: underline; }
    .mono{ font-variant-numeric: tabular-nums; }
    .id-pill{
      display:inline-flex; align-items:center; gap:.4rem;
      padding:.35rem .6rem; border-radius: 999px;
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.12);
      color: rgba(255,255,255,.80);
      font-size: .78rem;
    }
  </style>
</head>
<body>
<?php
  // helper kecil biar aman dari null (PHP 8.1/8.2)
  if(!function_exists('e')){
    function e($v){ return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
  }

  $ttl = (int)$this->config->item('qr_ttl_seconds','qr');
  $uid = (int)($user->id ?? 0);
?>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-7">
        <div class="glass p-4 p-md-5">
          <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
              <h3 class="mb-1">QR Absensi</h3>
              <div class="small-muted">Scan pakai kamera HP (lebih aman + ada lokasi).</div>

              <div class="small-muted mt-2 mono">
                <span id="liveClock">--</span>
              </div>
            </div>

            <span class="badge badge-soft px-3 py-2 rounded-pill mono">
              Expired: <?= date('Y-m-d H:i:s', (int)$exp) ?> WIB
            </span>
          </div>

          <hr class="border-opacity-25 my-4">

          <div class="text-center">
            <div class="qr-wrap mb-3">
              <img
                src="<?= site_url('qr/img/'.$uid).'?t='.time() ?>"
                alt="QR Absensi"
                style="width:260px;height:260px;"
              >
            </div>

            <div class="small-muted mb-3">
              Nama: <b><?= e($user->nama ?? '') ?></b>
            </div>

            <div class="d-grid d-sm-flex gap-2 justify-content-center">
              <a class="btn btn-light btn-premium" href="<?= e($url) ?>" target="_blank" rel="noopener">
                Buka Link (Testing)
              </a>
              <button class="btn btn-primary btn-premium" id="btnCopy" type="button">
                Copy Link
              </button>
            </div>

            <div class="mt-3 small-muted">
              *QR otomatis berubah tiap <?= $ttl ?> detik.
            </div>
          </div>
        </div>

        <div class="text-center mt-3 small-muted">
          © Absensi QR CI3 • Premium UI
        </div>
      </div>
    </div>
  </div>

<script>
(function(){
  // realtime clock
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

  // copy link (lebih aman daripada inline onclick)
  const url = <?= json_encode((string)($url ?? '')) ?>;
  const btn = document.getElementById('btnCopy');
  btn.addEventListener('click', async () => {
    try{
      await navigator.clipboard.writeText(url);
      btn.textContent = 'Tercopy ✓';
      setTimeout(()=>btn.textContent='Copy Link', 1200);
    }catch(e){
      // fallback
      const ta = document.createElement('textarea');
      ta.value = url;
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
      btn.textContent = 'Tercopy ✓';
      setTimeout(()=>btn.textContent='Copy Link', 1200);
    }
  });
})();
</script>
</body>
</html>
