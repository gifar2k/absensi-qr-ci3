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

<style>
:root{--r:20px;--s:rgba(255,255,255,.1);--g:rgba(255,255,255,.06);--m:rgba(229,231,235,.72);--t:#e5e7eb;--bg:#0b1220}
*{box-sizing:border-box}
body{
  font-family:Inter,system-ui;
  min-height:100vh;
  background:
    radial-gradient(1200px 600px at 20% 10%,rgba(99,102,241,.2),transparent 60%),
    radial-gradient(900px 500px at 80% 30%,rgba(16,185,129,.16),transparent 60%),
    var(--bg);
  color:var(--t);margin:0
}
.glass{background:var(--g);border:1px solid var(--s);border-radius:var(--r);backdrop-filter:blur(12px);box-shadow:0 18px 45px rgba(0,0,0,.35)}
.muted{color:var(--m)}
.topbar{position:sticky;top:0;z-index:9;padding:10px 0;background:linear-gradient(to bottom,rgba(11,18,32,.92),rgba(11,18,32,.35));border-bottom:1px solid rgba(255,255,255,.06)}
.brand-dot{width:10px;height:10px;border-radius:999px;background:#22c55e;box-shadow:0 0 0 5px rgba(34,197,94,.15);margin-right:8px}
.chip{border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);border-radius:999px;padding:.35rem .6rem;font-weight:700;font-size:.78rem;display:flex;justify-content:space-between;gap:6px}
.hint{display:flex;gap:10px;padding:10px 12px;border-radius:16px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)}
.hint .num{width:24px;height:24px;border-radius:8px;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;font-weight:800}
.table-wrap{flex:1;overflow:auto;border-radius:16px;border:1px solid rgba(255,255,255,.08)}
.table{margin:0;--bs-table-bg:transparent;--bs-table-color:#e5e7eb}
.table thead th{background:rgba(17,24,39,.92)!important;font-size:.78rem}
.table tbody td{font-size:.85rem}
.row-status{font-weight:900}
.st-belum-masuk{color:rgba(229,231,235,.6)}
.st-belum-pulang{color:#fbbf24}
.st-pulang{color:#34d399}

/* MOBILE COLLAPSE */
.mobile-only{display:none}
.collapse-shell{margin-top:8px;border-radius:16px;border:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.04)}
.collapse-head{display:flex;justify-content:space-between;align-items:center;padding:8px 10px}
.btn-collapse{border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);color:#e5e7eb;border-radius:12px;font-size:.75rem;padding:.3rem .5rem}
.collapse-body{max-height:0;overflow:hidden;transition:.25s}
.collapse-shell.open .collapse-body{max-height:400px}
.collapse-inner{display:grid;grid-template-columns:1fr 1fr;gap:6px;padding:8px 10px}

@media(max-width:576px){
.desktop-only{display:none!important}
.mobile-only{display:block}
}

.qr-card{padding:16px;display:flex;flex-direction:column;gap:12px;align-items:stretch}
.qr-head{display:flex;align-items:center;justify-content:space-between;gap:10px}
.qr-title{display:flex;align-items:center;gap:10px}
.qr-accent{width:10px;height:34px;border-radius:999px;background:linear-gradient(180deg,rgba(99,102,241,.95),rgba(16,185,129,.75));box-shadow:0 0 0 6px rgba(99,102,241,.10)}
.qr-h1{font-size:1.02rem;letter-spacing:.2px;line-height:1.1}
.qr-h2{line-height:1.2}

.qr-pill{font-size:.72rem;font-weight:800;letter-spacing:.7px;padding:.28rem .55rem;border-radius:999px;
  background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.25);color:#a7f3d0;white-space:nowrap}

.qr-frame{position:relative;width:100%;max-width:340px;aspect-ratio:1/1;margin:2px auto 0;
  padding:14px;border-radius:22px;background:rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.14);
  box-shadow:0 26px 70px rgba(0,0,0,.45),inset 0 0 0 1px rgba(255,255,255,.05);
  display:flex;align-items:center;justify-content:center;overflow:hidden}
.qr-glow{position:absolute;inset:-20%;background:
  radial-gradient(220px 220px at 30% 20%,rgba(99,102,241,.22),transparent 60%),
  radial-gradient(240px 240px at 70% 80%,rgba(16,185,129,.16),transparent 60%);
  filter:blur(2px);pointer-events:none}
.qr-img{position:relative;width:100%;height:100%;object-fit:contain;background:#fff;border-radius:16px;padding:10px}

.qr-badges{display:flex;gap:8px;flex-wrap:wrap;justify-content:center;margin-top:2px}
.badge-soft{padding:.34rem .65rem;border-radius:999px;font-size:.72rem;font-weight:800;
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);color:#e5e7eb}

.qr-note{display:flex;gap:10px;align-items:flex-start;padding:10px 12px;border-radius:16px;
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)}
.qr-note .dot{width:26px;height:26px;border-radius:10px;background:rgba(255,255,255,.06);
  display:flex;align-items:center;justify-content:center;font-weight:900}
.qr-note-body{line-height:1.2}

.qr-stats{display:flex;gap:8px;flex-wrap:wrap;justify-content:center;margin-top:2px}

/* Mobile micro-tune */
@media(max-width:576px){
  .qr-card{padding:14px;gap:10px}
  .qr-frame{padding:12px;border-radius:20px;max-width:420px}
  .qr-img{padding:8px;border-radius:14px}
  .qr-accent{height:32px}
  .qr-h1{font-size:1rem}
}

.monitor-footer{
  position:sticky;
  bottom:0;
  width:100%;
  background:linear-gradient(to top,rgba(11,18,32,.95),rgba(11,18,32,.55));
  border-top:1px solid rgba(255,255,255,.06);
  backdrop-filter:blur(10px);
  padding:8px 16px;
}

.footer-inner{
  display:flex;
  justify-content:center;
  gap:18px;
  font-size:.75rem;
  color:rgba(229,231,235,.75);
  flex-wrap:wrap;
}
.footer-inner b{color:#fff}


</style>
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
<div class="container-fluid px-3">
<div class="d-flex justify-content-between align-items-center gap-2">
<div class="d-flex align-items-center">
<span class="brand-dot"></span>
<div>
<div class="fw-bold" style="font-size:1rem">I-LOG COMPUTER Absensi</div>
<div class="muted small"><?= htmlspecialchars($workday_day) ?> • <?= htmlspecialchars($workday) ?></div>
</div>
</div>

<!-- DESKTOP CHIPS -->
<div class="desktop-only d-flex gap-2">
<span class="chip">QR<b><?= (int)$refresh_seconds ?>s</b></span>
<span class="chip">Poll<b><?= (int)$poll_seconds ?>s</b></span>
<span class="chip">Srv<b id="serverTime">--</b></span>
</div>
</div>



<!-- MOBILE COLLAPSE -->
<div class="mobile-only">
<div class="collapse-shell" id="mShell">
<div class="collapse-head">
<div class="fw-bold small">Live Monitor</div>
<button class="btn-collapse" id="btnToggle">Detail</button>
</div>
<div class="collapse-body">
<div class="collapse-inner">
<span class="chip">Server<b id="serverTimeM">--</b></span>
<span class="chip">Workday<b id="workdayDateM">-</b></span>
<span class="chip">Total<b id="sumTotalM">0</b></span>
<span class="chip">Masuk<b id="sumBelumMasukM">0</b></span>
<span class="chip">Belum<b id="sumBelumPulangM">0</b></span>
<span class="chip">Pulang<b id="sumPulangM">0</b></span>
</div>
</div>
</div>
</div>

</div>
</div>

<main class="container-fluid px-3 py-3">
<div class="row g-3">

<!-- QR -->
<div class="col-12 col-lg-4">
  <div class="glass qr-card h-100">
    <div class="qr-head">
      <div class="qr-title">
        <span class="qr-accent"></span>
        <div>
          <div class="fw-bold qr-h1">Scan QR Absensi</div>
          <div class="muted small qr-h2">Scan → Email → Masuk / Pulang</div>
        </div>
      </div>
      <span class="qr-pill">LIVE</span>
    </div>

    <div class="qr-frame">
      <div class="qr-glow"></div>
      <img id="qrImg" class="qr-img" src="<?= site_url('qr/png') ?>?v=<?= time() ?>" alt="QR Absensi">
    </div>

    <div class="qr-badges">
      <span class="badge-soft">Auto Refresh</span>
      <span class="badge-soft">Lokasi Aktif</span>
      <span class="badge-soft">1 Device</span>
    </div>

    <div class="qr-note">
      <div class="dot">i</div>
      <div class="qr-note-body">
        <div class="fw-semibold">Info Absensi</div>
        <div class="muted small">Masuk terlebih dulu sebelum Pulang</div>
      </div>
    </div>

    <div class="desktop-only qr-stats">
      <span class="chip">Total <b id="sumTotal">0</b></span>
      <span class="chip">Masuk <b id="sumBelumMasuk">0</b></span>
      <span class="chip">Belum <b id="sumBelumPulang">0</b></span>
      <span class="chip">Pulang <b id="sumPulang">0</b></span>
      <span class="chip muted" id="feedInfo">Memuat…</span>
    </div>
  </div>
</div>


<!-- TABLE -->
<div class="col-12 col-lg-8">
<div class="glass p-3 h-100 d-flex flex-column">
<div class="fw-bold mb-2">Status Kehadiran Hari Ini</div>
<div class="table-wrap">
<table class="table table-hover">
<thead><tr><th>Nama</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr></thead>
<tbody id="tbody"><tr><td colspan="4" class="muted">Loading…</td></tr></tbody>
</table>
</div>
</div>
</div>

<footer class="monitor-footer desktop-only">
  <div class="footer-inner">
    <span>Last update: <b id="serverTimeFoot">--:--:--</b></span>
    <span>Mode: <b>QR Absensi</b></span>
    <span>Lokasi: <b>Aktif</b></span>
    <span>Device: <b>1 User</b></span>
    <span class="muted">I-LOG Computer © 2025</span>
  </div>
</footer>

</div>
</main>

<script>
(()=>{

const POLL=<?= (int)$poll_seconds ?>*1000,QR=<?= (int)$refresh_seconds ?>*1000;
const qs=id=>document.getElementById(id);
const mobile={shell:qs('mShell'),btn:qs('btnToggle')};
mobile.btn?.addEventListener('click',()=>mobile.shell.classList.toggle('open'));

async function poll(){
try{
const r=await fetch("<?= site_url('monitor/feed') ?>",{cache:'no-store'});
const j=await r.json();if(!j.ok)return;
const d=j.data||{},rows=d.rows||[];
qs('serverTime').textContent=d.server_time||'--';
qs('serverTimeM').textContent=d.server_time||'--';
qs('workdayDateM').textContent=d.workday_date||'-';
let bm=0,bp=0,p=0;
rows.forEach(r=>{if(r.status==='PULANG')p++;else if(r.status==='BELUM PULANG')bp++;else bm++;});
['sumTotal','sumTotalM'].forEach(i=>qs(i).textContent=rows.length);
['sumBelumMasuk','sumBelumMasukM'].forEach(i=>qs(i).textContent=bm);
['sumBelumPulang','sumBelumPulangM'].forEach(i=>qs(i).textContent=bp);
['sumPulang','sumPulangM'].forEach(i=>qs(i).textContent=p);
qs('feedInfo').textContent=`${rows.length} Staff`;
qs('tbody').innerHTML=rows.map(r=>`
<tr>
<td><b>${r.name}</b><div class="muted small">${r.email}</div></td>
<td>${r.jam_masuk||'-'}</td>
<td>${r.jam_pulang||'-'}</td>
<td class="row-status ${r.status==='PULANG'?'st-pulang':r.status==='BELUM PULANG'?'st-belum-pulang':'st-belum-masuk'}">${r.status}</td>
</tr>`).join('');
}catch(e){}
}

setInterval(()=>qs('qrImg').src="<?= site_url('qr/png') ?>?v="+Date.now(),QR);
poll();setInterval(poll,POLL);

})();
</script>


</body>
</html>
