<?php
// optional: amanin variable
$adminName = $admin['name'] ?? '-';
$adminRole = $admin['role'] ?? '-';
?>

<div class="d-flex align-items-start align-items-md-center justify-content-between mb-4 gap-3">
  <div>
    <div class="d-flex align-items-center gap-2 mb-1">
      <div class="badge rounded-pill text-bg-primary bg-opacity-25 border border-primary border-opacity-25">
        <i class="bi bi-shield-lock me-1"></i> Admin Panel
      </div>
      <?php if (($adminRole ?? '') === 'superadmin'): ?>
        <div class="badge rounded-pill text-bg-warning bg-opacity-25 border border-warning border-opacity-25">
          <i class="bi bi-stars me-1"></i> Superadmin
        </div>
      <?php endif; ?>
    </div>

    <h3 class="mb-1 fw-bold">Dashboard</h3>
    <div class="small muted">
      Ringkasan sistem absensi QR ·
      <span class="text-white fw-semibold"><?= html_escape($adminName) ?></span>
      <span class="mx-1">·</span>
      <span class="text-white-50"><?= html_escape($adminRole) ?></span>
    </div>
  </div>

  <div class="d-flex gap-2 flex-wrap">
    <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-light border-opacity-25">
      <i class="bi bi-people me-1"></i> Kelola Users
    </a>
    <a href="<?= site_url('admin/logs') ?>" class="btn btn-primary">
      <i class="bi bi-list-check me-1"></i> Lihat Logs
    </a>
  </div>
</div>

<!-- STATS -->
<div class="row g-3 mb-3">
  <div class="col-12 col-md-6 col-xl-3">
    <div class="card card-glass p-3 h-100">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="small muted">Status Panel</div>
          <div class="fs-5 fw-semibold mt-1">Aktif</div>
          <div class="small muted mt-1">Login valid & session berjalan</div>
        </div>
        <div class="stat-icon">
          <i class="bi bi-check2-circle"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="card card-glass p-3 h-100">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="small muted">Pegawai</div>
          <div class="fs-5 fw-semibold mt-1">Kelola User</div>
          <div class="small muted mt-1">Aktif/nonaktif & device lock</div>
        </div>
        <div class="stat-icon">
          <i class="bi bi-people"></i>
        </div>
      </div>
      <div class="mt-3">
        <a href="<?= site_url('admin/users') ?>" class="btn btn-sm btn-outline-light border-opacity-25 w-100">
          Buka Kelola Users
        </a>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="card card-glass p-3 h-100">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="small muted">Logs Absensi</div>
          <div class="fs-5 fw-semibold mt-1">Audit Trail</div>
          <div class="small muted mt-1">Filter, cari, export CSV</div>
        </div>
        <div class="stat-icon">
          <i class="bi bi-list-check"></i>
        </div>
      </div>
      <div class="mt-3">
        <a href="<?= site_url('admin/logs') ?>" class="btn btn-sm btn-outline-light border-opacity-25 w-100">
          Buka Logs
        </a>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-3">
    <div class="card card-glass p-3 h-100">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="small muted">Settings Kantor</div>
          <div class="fs-5 fw-semibold mt-1">Geofence</div>
          <div class="small muted mt-1">Lat/Lng, radius, refresh QR</div>
        </div>
        <div class="stat-icon">
          <i class="bi bi-gear"></i>
        </div>
      </div>
      <div class="mt-3">
        <a href="<?= site_url('admin/settings') ?>" class="btn btn-sm btn-outline-light border-opacity-25 w-100">
          Buka Settings
        </a>
      </div>
    </div>
  </div>
</div>

<!-- QUICK ACTIONS + STATUS -->
<div class="row g-3">
  <div class="col-12 col-xl-7">
    <div class="card card-glass p-3 p-md-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <div class="fw-bold">Quick Actions</div>
          <div class="small muted">Akses cepat fitur penting</div>
        </div>
        <div class="badge rounded-pill text-bg-light bg-opacity-10 border border-light border-opacity-10">
          <i class="bi bi-lightning-charge me-1"></i> Productivity
        </div>
      </div>

      <div class="row g-2">
        <div class="col-12 col-md-6">
          <a class="qa-tile" href="<?= site_url('admin/users') ?>">
            <div class="qa-ico"><i class="bi bi-person-plus"></i></div>
            <div>
              <div class="fw-semibold">Tambah / Edit Pegawai</div>
              <div class="small muted">Aktifkan/nonaktifkan & email</div>
            </div>
            <div class="qa-arrow"><i class="bi bi-arrow-right"></i></div>
          </a>
        </div>

        <div class="col-12 col-md-6">
          <a class="qa-tile" href="<?= site_url('admin/logs') ?>">
            <div class="qa-ico"><i class="bi bi-funnel"></i></div>
            <div>
              <div class="fw-semibold">Filter Logs</div>
              <div class="small muted">Tanggal, user, status IN/OUT</div>
            </div>
            <div class="qa-arrow"><i class="bi bi-arrow-right"></i></div>
          </a>
        </div>

        <div class="col-12 col-md-6">
          <a class="qa-tile" href="<?= site_url('admin/rekap') ?>">
            <div class="qa-ico"><i class="bi bi-calendar-week"></i></div>
            <div>
              <div class="fw-semibold">Rekap Harian</div>
              <div class="small muted">Ringkasan per hari kerja</div>
            </div>
            <div class="qa-arrow"><i class="bi bi-arrow-right"></i></div>
          </a>
        </div>

        <div class="col-12 col-md-6">
          <a class="qa-tile" href="<?= site_url('admin/settings') ?>">
            <div class="qa-ico"><i class="bi bi-geo-alt"></i></div>
            <div>
              <div class="fw-semibold">Update Lokasi Kantor</div>
              <div class="small muted">Strict radius & token window</div>
            </div>
            <div class="qa-arrow"><i class="bi bi-arrow-right"></i></div>
          </a>
        </div>
      </div>

      <div class="mt-3 small muted">
        Tips: set <span class="text-white fw-semibold">token window ≥ QR refresh</span>, dan radius jangan terlalu kecil biar GPS nggak gagal.
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-5">
    <div class="card card-glass p-3 p-md-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <div class="fw-bold">System Checklist</div>
          <div class="small muted">Status modul penting</div>
        </div>
        <div class="badge rounded-pill text-bg-success bg-opacity-25 border border-success border-opacity-25">
          <i class="bi bi-check2-all me-1"></i> Ready
        </div>
      </div>

      <div class="checklist">
        <div class="check-row">
          <i class="bi bi-check2-circle text-success"></i>
          <div>
            <div class="fw-semibold">Admin Auth</div>
            <div class="small muted">Login admin/superadmin + session</div>
          </div>
        </div>

        <div class="check-row">
          <i class="bi bi-check2-circle text-success"></i>
          <div>
            <div class="fw-semibold">Users & Device Lock</div>
            <div class="small muted">Kelola pegawai + reset device</div>
          </div>
        </div>

        <div class="check-row">
          <i class="bi bi-check2-circle text-success"></i>
          <div>
            <div class="fw-semibold">Logs & Export</div>
            <div class="small muted">Audit + CSV export</div>
          </div>
        </div>

        <div class="check-row">
          <i class="bi bi-info-circle text-info"></i>
          <div>
            <div class="fw-semibold">Monitor TV Real-time</div>
            <div class="small muted">Polling feed + QR refresh</div>
          </div>
        </div>
      </div>

      <div class="mt-3">
        <a href="<?= site_url('/') ?>" class="btn btn-outline-light border-opacity-25 w-100">
          <i class="bi bi-display me-1"></i> Buka Monitor TV
        </a>
      </div>

      <div class="mt-2 small muted">
        Monitor akan menampilkan status turunan: <span class="text-white fw-semibold">BELUM MASUK</span>, <span class="text-white fw-semibold">BELUM PULANG</span>, <span class="text-white fw-semibold">PULANG</span>.
      </div>
    </div>
  </div>
</div>

<style>
/* icon box di stats */
.stat-icon{
  width: 46px;
  height: 46px;
  border-radius: 14px;
  display:flex;
  align-items:center;
  justify-content:center;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.08);
}
.stat-icon i{
  font-size: 1.3rem;
  opacity: .95;
}

/* quick action tile */
.qa-tile{
  display:flex;
  align-items:center;
  gap:12px;
  padding: 12px;
  border-radius: 16px;
  text-decoration:none;
  color: var(--text);
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(255,255,255,.08);
  transition: .15s ease;
}
.qa-tile:hover{
  background: rgba(255,255,255,.06);
  border-color: rgba(255,255,255,.12);
  color: #fff;
}
.qa-ico{
  width: 40px;
  height: 40px;
  border-radius: 14px;
  display:flex;
  align-items:center;
  justify-content:center;
  background: linear-gradient(135deg, rgba(79,140,255,.25), rgba(122,167,255,.12));
  border: 1px solid rgba(79,140,255,.25);
}
.qa-ico i{ font-size: 1.15rem; }
.qa-arrow{
  margin-left:auto;
  opacity: .7;
}

/* checklist rows */
.checklist{ display:flex; flex-direction:column; gap:12px; }
.check-row{
  display:flex;
  gap:10px;
  align-items:flex-start;
  padding: 10px 12px;
  border-radius: 14px;
  background: rgba(255,255,255,.03);
  border: 1px solid rgba(255,255,255,.06);
}
.check-row i{ font-size: 1.1rem; margin-top:2px; }
</style>
