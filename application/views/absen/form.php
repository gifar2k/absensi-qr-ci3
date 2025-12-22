<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body{
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      min-height: 100vh;
      background:
        radial-gradient(900px 500px at 20% 10%, rgba(99,102,241,.18), transparent 60%),
        radial-gradient(900px 500px at 80% 30%, rgba(16,185,129,.14), transparent 60%),
        #0b1220;
      color:#e5e7eb;
    }
    .glass{
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.10);
      box-shadow: 0 20px 60px rgba(0,0,0,.35);
      backdrop-filter: blur(12px);
      border-radius: 22px;
    }
    .muted{ color: rgba(229,231,235,.72); }
    .btn-big{ padding: 14px 16px; border-radius: 16px; font-weight: 700; }
    .input-dark{
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.12);
      color:#e5e7eb;
      border-radius: 16px;
      padding: 14px 14px;
    }
    .input-dark:focus{
      background: rgba(255,255,255,.08);
      border-color: rgba(255,255,255,.22);
      color:#fff;
      box-shadow: none;
    }
    .pill{
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.06);
      padding: .45rem .75rem;
      border-radius: 999px;
      font-weight: 600;
      font-size: .85rem;
      color:#e5e7eb;
    }
    .status-box{
      border: 1px dashed rgba(255,255,255,.18);
      border-radius: 18px;
      padding: 12px 14px;
      background: rgba(255,255,255,.04);
    }
  </style>
</head>

<body>
  <main class="container py-4 py-md-5">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-6">
        <div class="glass p-4 p-md-5">
          <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
              <div class="fw-bold" style="font-size:1.35rem;">Absensi Pegawai</div>
              <div class="muted mt-1"><?= htmlspecialchars($workday_label) ?></div>
            </div>
            <div class="pill">Mode 2</div>
          </div>

          <div class="status-box mt-4">
            <div class="small muted">Status QR</div>
            <div id="qrStatus" class="fw-semibold">Token terdeteksi ✅</div>
            <div class="small muted mt-1">Pastikan lokasi aktif & berada di radius kantor.</div>
          </div>

          <div class="mt-4">
            <label class="form-label muted">Email</label>
            <input id="email" class="form-control input-dark" type="email" placeholder="" autocomplete="email">
          </div>

          <div class="row g-2 mt-3">
            <div class="col-6">
              <button id="btnIn" class="btn btn-success w-100 btn-big" type="button">MASUK</button>
            </div>
            <div class="col-6">
              <button id="btnOut" class="btn btn-outline-light w-100 btn-big" type="button">PULANG</button>
            </div>
          </div>

          <div class="mt-3 small muted" id="locInfo">Lokasi: belum diambil</div>

          <div class="mt-4 d-grid">
            <button id="btnSubmit" class="btn btn-primary w-100 btn-lg" type="button">
              Submit Absensi
            </button>
            <div id="submitHint" class="small text-muted mt-2" style="min-height:18px;"></div>
          </div>

          <div class="small muted mt-3">
            Jika muncul “device terkunci”, hubungi admin untuk reset device.
          </div>
        </div>
      </div>
    </div>
  </main>

<script>
(function(){
  const token = "<?= htmlspecialchars($token ?? '') ?>";
  const qrStatus = document.getElementById('qrStatus');
  const emailEl = document.getElementById('email');
  const btnIn = document.getElementById('btnIn');
  const btnOut = document.getElementById('btnOut');
  const btnSubmit = document.getElementById('btnSubmit');
  const submitHint = document.getElementById('submitHint');
  const locInfo = document.getElementById('locInfo');

  const KEY = "ilog_absensi_client_id";
  let clientId = localStorage.getItem(KEY);
  if (!clientId) {
    clientId = (crypto?.randomUUID ? crypto.randomUUID() : (Date.now()+"-"+Math.random().toString(16).slice(2)));
    localStorage.setItem(KEY, clientId);
  }

  let action = null;
  let coords = { lat:null, lng:null, accuracy:null };

  const SubmitGuard = {
    locked: false,
    timer: null,
    minCooldownSec: 5,
    startedAt: 0,
    startCooldown(sec = 5){
      this.locked = true;
      this.startedAt = Date.now();
      let left = sec;
      btnSubmit.disabled = true;
      btnSubmit.textContent = `Tunggu ${left}s`;
      submitHint.textContent = `Anti spam aktif. Coba lagi dalam ${left} detik.`;

      if (this.timer) clearInterval(this.timer);
      this.timer = setInterval(() => {
        left--;
        if (left <= 0){
          clearInterval(this.timer);
          this.timer = null;
          this.locked = false;
          btnSubmit.textContent = 'Submit Absensi';
          submitHint.textContent = '';
          validateReady();
          return;
        }
        btnSubmit.textContent = `Tunggu ${left}s`;
        submitHint.textContent = `Anti spam aktif. Coba lagi dalam ${left} detik.`;
      }, 1000);
    }
  };

  function setAction(a){
    action = a;

    if (a === 'IN'){
      btnIn.classList.remove('btn-outline-light');
      btnIn.classList.add('btn-success');
      btnOut.classList.remove('btn-success');
      btnOut.classList.add('btn-outline-light');
    } else {
      btnOut.classList.remove('btn-outline-light');
      btnOut.classList.add('btn-success');
      btnIn.classList.remove('btn-success');
      btnIn.classList.add('btn-outline-light');
    }
    validateReady();
  }

  function validateReady(){
    const emailOk = emailEl.value.trim().length >= 5 && emailEl.value.includes('@');
    const locOk = coords.lat !== null && coords.lng !== null;
    const tokenOk = token && token.length > 10;

    if (SubmitGuard.locked) {
      btnSubmit.disabled = true;
      return;
    }
    btnSubmit.disabled = !(emailOk && locOk && tokenOk && action);
  }

  function geoErrorText(err){
    // biar ketahuan di HP itu fail karena apa
    if (!err) return 'Lokasi gagal.';
    if (err.code === 1) return 'Izin lokasi ditolak. Aktifkan permission lokasi di browser.';
    if (err.code === 2) return 'Lokasi tidak tersedia. Nyalakan GPS & coba lagi.';
    if (err.code === 3) return 'Timeout ambil lokasi. Coba pindah ke area terbuka / nyalakan High Accuracy.';
    return 'Lokasi gagal. Coba lagi.';
  }

  function getLocation(){
    if (!navigator.geolocation){
      locInfo.textContent = "Browser tidak support lokasi.";
      return;
    }

    locInfo.textContent = "Mengambil lokasi...";
    navigator.geolocation.getCurrentPosition((pos)=>{
      coords.lat = pos.coords.latitude;
      coords.lng = pos.coords.longitude;
      coords.accuracy = pos.coords.accuracy;
      locInfo.textContent = `Lokasi OK • akurasi ~${Math.round(coords.accuracy)}m`;
      validateReady();
    }, (err)=>{
      coords.lat = coords.lng = coords.accuracy = null;
      locInfo.textContent = geoErrorText(err);
      validateReady();
    }, {
      enableHighAccuracy: true,
      timeout: 12000,
      maximumAge: 0
    });
  }

  function refreshLocationOnce(){
    return new Promise((resolve) => {
      if (!navigator.geolocation) return resolve(false);
      navigator.geolocation.getCurrentPosition((pos)=>{
        coords.lat = pos.coords.latitude;
        coords.lng = pos.coords.longitude;
        coords.accuracy = pos.coords.accuracy;
        locInfo.textContent = `Lokasi OK • akurasi ~${Math.round(coords.accuracy)}m`;
        resolve(true);
      }, (err)=>{
        coords.lat = coords.lng = coords.accuracy = null;
        locInfo.textContent = geoErrorText(err);
        resolve(false);
      }, { enableHighAccuracy:true, timeout:8000, maximumAge:0 });
    });
  }

  async function doSubmit(){
    const fd = new FormData();
    fd.append('token', token);
    fd.append('email', emailEl.value.trim());
    fd.append('action', action);
    fd.append('lat', coords.lat);
    fd.append('lng', coords.lng);
    fd.append('accuracy', coords.accuracy ?? '');
    fd.append('client_id', clientId);

    const res = await fetch("<?= site_url('absen/submit') ?>", { method:'POST', body: fd });
    const text = await res.text();

    let json;
    try { json = JSON.parse(text); }
    catch(e){
      await Swal.fire({
        icon:'error',
        title:'Server Error',
        html:`<div class="text-start">
          <div><b>HTTP:</b> ${res.status}</div>
          <div class="mt-2 small text-muted">Response:</div>
          <pre class="small" style="white-space:pre-wrap">${(text || '').slice(0,500)}</pre>
        </div>`
      });
      return;
    }

    if (!json.ok){
      const d = json.data || {};
      const extra = (d && Object.keys(d).length)
        ? `
          <div class="text-start mt-2 small">
            <div><b>Code:</b> ${d.code || '-'}</div>
            <div><b>Distance:</b> ${d.distance_m ?? '-'} m</div>
            <div><b>Radius:</b> ${d.radius_m ?? '-'} m</div>
            <div><b>Accuracy:</b> ${d.accuracy_m ?? '-'} m</div>
            <hr class="my-2">
            <div><b>User:</b> ${d.user_lat ?? '-'}, ${d.user_lng ?? '-'}</div>
            <div><b>Office:</b> ${d.office_lat ?? '-'}, ${d.office_lng ?? '-'}</div>
          </div>
        ` : '';

      await Swal.fire({
        icon:'warning',
        title:'Ditolak',
        html: `<div>${json.message}</div>${extra}`,
        confirmButtonText: 'OK'
      });
      return;
    }

    // ✅ SUCCESS
const d = json.data || {};
await Swal.fire({
  icon:'success',
  title: json.message || 'Berhasil',
  html: `
    <div class="text-start mt-2">
      <div><b>${(d.name || '').toString()}</b></div>
      <div class="text-muted">${(d.email || '').toString()}</div>
      <hr>
      <div>Status: <b>${d.status === 'IN' ? 'MASUK' : 'PULANG'}</b></div>
      <div>Jam: <b>${d.time || '-'}</b></div>
      <div>Jarak: <b>${(d.distance_m ?? '-')} m</b></div>
      <div class="small text-muted mt-1">Workday: ${d.workday_date || '-'}</div>
    </div>
  `,
  confirmButtonText: 'OK',
  allowOutsideClick: false,
  allowEscapeKey: false
});


window.location.href = "<?= site_url('/') ?>";
  }

  // QR token check
  if (!token || token.length < 10){
    qrStatus.textContent = "Token tidak ada / invalid. Scan ulang QR.";
  }

  btnIn.addEventListener('click', ()=>setAction('IN'));
  btnOut.addEventListener('click', ()=>setAction('OUT'));
  emailEl.addEventListener('input', validateReady);

  // ✅ default pilih MASUK biar action nggak null
  setAction('IN');

  btnSubmit.addEventListener('click', async ()=>{
    if (SubmitGuard.locked) return;
    if (btnSubmit.disabled) return;

    SubmitGuard.startCooldown(SubmitGuard.minCooldownSec);

    try {
      const ok = await refreshLocationOnce();
      validateReady();
      if (!ok){
        Swal.fire({ icon:'warning', title:'Lokasi belum siap', text:'Aktifkan GPS & izin lokasi, lalu coba lagi.' });
        return;
      }
      await doSubmit();
    } catch(e){
      Swal.fire({ icon:'error', title:'Error', text:'Koneksi/Server bermasalah. Coba lagi.' });
    }
  });

  // auto get location on load
  getLocation();
})();
</script>



</body>
</html>
