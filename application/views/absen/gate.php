<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Memproses Absensi…</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg1:#0b1220;--bg2:#0f1b33;--card:rgba(255,255,255,.08);--border:rgba(255,255,255,.14);--text:rgba(255,255,255,.92);--muted:rgba(255,255,255,.65);}
    body{font-family:Inter,system-ui;min-height:100vh;color:var(--text);
      background:radial-gradient(1200px 600px at 15% 10%, rgba(34,211,238,.18), transparent 60%),
               radial-gradient(1200px 600px at 85% 15%, rgba(167,139,250,.18), transparent 55%),
               linear-gradient(180deg,var(--bg1),var(--bg2));}
    .glass{background:var(--card);border:1px solid var(--border);border-radius:24px;backdrop-filter:blur(14px);box-shadow:0 20px 50px rgba(0,0,0,.35);}
    .muted{color:var(--muted);}
  </style>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-7">
      <div class="glass p-4 p-md-5 text-center">
        <div class="spinner-border" role="status" aria-hidden="true"></div>
        <h3 class="mt-3 mb-1">Memproses Absensi…</h3>
        <div class="muted mb-3" id="status">Mengambil lokasi…</div>

        <button class="btn btn-outline-light rounded-4 px-4" id="btnRetry" style="display:none;">Coba lagi</button>
        <button class="btn btn-light rounded-4 px-4 ms-2" id="btnSkip" style="display:none;">Lanjut tanpa lokasi</button>
      </div>
      <div class="text-center mt-3 muted small">Tips: aktifkan GPS & izinkan lokasi.</div>
    </div>
  </div>
</div>

<script>
(function(){
  const token = <?= json_encode((string)$token) ?>;
  const submitUrl = <?= json_encode((string)$submit_url) ?>;
  const statusEl = document.getElementById('status');
  const btnRetry = document.getElementById('btnRetry');
  const btnSkip  = document.getElementById('btnSkip');

  function go(lat,lng){
    const url = new URL(submitUrl, window.location.origin);
    url.searchParams.set('token', token);
    if(lat!=null && lng!=null){
      url.searchParams.set('lat', lat);
      url.searchParams.set('lng', lng);
    }
    window.location.href = url.toString();
  }

  function failUI(msg){
    statusEl.textContent = msg;
    btnRetry.style.display='inline-block';
    btnSkip.style.display='inline-block';
  }

  function requestLocation(){
    statusEl.textContent = "Mengambil lokasi…";
    btnRetry.style.display='none';
    btnSkip.style.display='none';

    if(!navigator.geolocation){
      failUI("Browser tidak mendukung geolocation.");
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (pos)=>{
        statusEl.textContent = "Lokasi OK. Mengirim absensi…";
        setTimeout(()=>go(pos.coords.latitude, pos.coords.longitude), 250);
      },
      (err)=>{
        let msg = "Gagal ambil lokasi.";
        if(err && err.code===1) msg="Izin lokasi ditolak. Coba lagi atau lanjut tanpa lokasi.";
        if(err && err.code===2) msg="Lokasi tidak tersedia. Pastikan GPS aktif.";
        if(err && err.code===3) msg="Timeout ambil lokasi. Coba lagi.";
        failUI(msg);
      },
      { enableHighAccuracy:true, timeout:9000, maximumAge:0 }
    );
  }

  btnRetry.addEventListener('click', requestLocation);
  btnSkip.addEventListener('click', ()=>go(null,null));
  requestLocation();
})();
</script>
</body>
</html>
