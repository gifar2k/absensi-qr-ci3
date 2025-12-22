<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>QR Monitor • Absensi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg1:#0b1220;--bg2:#0f1b33;--card:rgba(255,255,255,.08);--border:rgba(255,255,255,.14);--text:rgba(255,255,255,.92);--muted:rgba(255,255,255,.65);}
    body{font-family:Inter,system-ui;background:
      radial-gradient(1200px 600px at 15% 10%, rgba(125,211,252,.20), transparent 60%),
      radial-gradient(1200px 600px at 85% 10%, rgba(167,139,250,.18), transparent 55%),
      linear-gradient(180deg,var(--bg1),var(--bg2));
      min-height:100vh;color:var(--text);}
    .glass{background:var(--card);border:1px solid var(--border);border-radius:28px;backdrop-filter:blur(14px);box-shadow:0 20px 50px rgba(0,0,0,.35);}
    .qr{background:#fff;border-radius:22px;padding:18px;display:inline-flex;box-shadow:0 18px 40px rgba(0,0,0,.25);}
    .muted{color:var(--muted);}
    .mono{font-variant-numeric: tabular-nums;}
    .big{font-size:clamp(28px, 4vw, 46px); font-weight:800; letter-spacing:-0.02em;}
  </style>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-10">
      <div class="glass p-4 p-md-5 text-center">
        <div class="big mb-2">SCAN QR UNTUK ABSEN</div>
        <div class="muted mb-4">Arahkan kamera HP ke QR. Lokasi akan dicek otomatis.</div>

        <!-- QR IMG pakai endpoint qr/img? tapi kita belum bikin.
             Cara paling cepat: pakai layanan chart? NO. Jadi kita pakai teks url + QR generator kamu yg lama? 
             Untuk cepat: pakai endpoint qrlib tetap bisa (Qr::img) tapi itu QR user. 
             Solusi: bikin endpoint kecil generate QR url (lihat catatan di bawah). -->

        <div class="qr mb-3">
          <img src="<?= site_url('qr/monitor_img?exp='.$exp.'&sig='.$this->security->xss_clean($this->input->get('sig')) ) ?>" style="width:340px;height:340px" alt="QR Monitor">
        </div>

        <div class="muted mono">
          Berlaku sampai: <b><?= date('Y-m-d H:i:s', (int)$exp) ?> WIB</b> • Refresh otomatis tiap <b><?= (int)$ttl ?>s</b>
        </div>

        <div class="mt-4 muted small mono" id="countdown">--</div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const exp = <?= (int)$exp ?> * 1000;
  const el = document.getElementById('countdown');
  function tick(){
    const left = Math.max(0, Math.floor((exp - Date.now())/1000));
    el.textContent = "QR refresh dalam " + left + " detik";
    if(left<=0) location.reload();
  }
  tick(); setInterval(tick, 1000);
})();
</script>
</body>
</html>
