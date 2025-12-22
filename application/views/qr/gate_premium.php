<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Memproses Absensi…</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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
        radial-gradient(1200px 600px at 15% 10%, rgba(34,211,238,.18), transparent 60%),
        radial-gradient(1200px 600px at 85% 15%, rgba(167,139,250,.18), transparent 55%),
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
    .dot{
      display:inline-block; width:10px; height:10px; border-radius:999px;
      background: rgba(34,197,94,.9);
      box-shadow: 0 0 0 6px rgba(34,197,94,.16);
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-7">
        <div class="glass p-4 p-md-5 text-center">
          <div class="d-flex justify-content-center mb-3"><span class="dot"></span></div>

          <h3 class="mb-2">Memproses Absensi…</h3>
          <div class="muted mono mb-2" id="liveClock">--</div>

          <div class="muted mb-4">
            Sedang mengambil lokasi untuk validasi. Mohon izinkan akses lokasi.
          </div>

          <div class="d-flex justify-content-center mb-3">
            <div class="spinner-border" role="status" aria-hidden="true"></div>
          </div>

          <div id="status" class="muted small">
            Menunggu izin lokasi…
          </div>

          <div class="mt-4">
            <button class="btn btn-outline-light rounded-4 px-4" id="btnRetry" style="display:none;">
              Coba lagi
            </button>
            <button class="btn btn-light rounded-4 px-4" id="btnSkip" style="display:none;">
              Lanjut tanpa lokasi
            </button>
          </div>
        </div>

        <div class="text-center mt-3 muted small">
          Tips: aktifkan GPS + gunakan browser (Chrome/Safari) supaya lokasi akurat.
        </div>
      </div>
    </div>
  </div>

<script>
(function(){
  // live clock
  const el = document.getElementById('liveClock');
  const days = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
  const months = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
  const pad = (n)=>String(n).padStart(2,'0');
  function tick(){
    const d = new Date();
    el.textContent = `${days[d.getDay()]}, ${pad(d.getDate())} ${months[d.getMonth()]} ${d.getFullYear()} • ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())} WIB`;
  }
  tick(); setInterval(tick, 1000);
})();
</script>

<script>
(function(){
  const uid = "<?= htmlspecialchars($uid) ?>";
  const exp = "<?= htmlspecialchars($exp) ?>";
  const sig = "<?= htmlspecialchars($sig) ?>";
  const scanUrl = "<?= htmlspecialchars($scan_url) ?>";

  const statusEl = document.getElementById('status');
  const btnRetry = document.getElementById('btnRetry');
  const btnSkip  = document.getElementById('btnSkip');

  function go(lat, lng){
    const url = new URL(scanUrl, window.location.origin);
    url.searchParams.set('uid', uid);
    url.searchParams.set('exp', exp);
    url.searchParams.set('sig', sig);
    if(lat != null && lng != null){
      url.searchParams.set('lat', lat);
      url.searchParams.set('lng', lng);
    }
    window.location.href = url.toString();
  }

  function failUI(msg){
    statusEl.textContent = msg;
    btnRetry.style.display = 'inline-block';
    btnSkip.style.display  = 'inline-block';
  }

  function requestLocation(){
    statusEl.textContent = "Mengambil lokasi…";
    btnRetry.style.display = 'none';
    btnSkip.style.display  = 'none';

    if(!navigator.geolocation){
      failUI("Browser tidak mendukung geolocation. Klik 'Lanjut tanpa lokasi'.");
      return;
    }

    const opt = { enableHighAccuracy: true, timeout: 9000, maximumAge: 0 };

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        statusEl.textContent = "Lokasi OK. Mengirim absensi…";
        setTimeout(()=>go(lat, lng), 350);
      },
      (err) => {
        let msg = "Gagal ambil lokasi.";
        if(err && err.code === 1) msg = "Izin lokasi ditolak. Klik 'Coba lagi' atau 'Lanjut tanpa lokasi'.";
        if(err && err.code === 2) msg = "Lokasi tidak tersedia. Pastikan GPS aktif.";
        if(err && err.code === 3) msg = "Timeout ambil lokasi. Coba lagi.";
        failUI(msg);
      },
      opt
    );
  }

  btnRetry.addEventListener('click', requestLocation);
  btnSkip.addEventListener('click', ()=>go(null, null));

  requestLocation();
})();
</script>
</body>
</html>
