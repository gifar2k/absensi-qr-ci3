<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-1">Settings Kantor</h4>
    <div class="small muted">Konfigurasi lokasi, radius absensi, dan secret QR.</div>
  </div>
</div>

<?php if ($this->session->flashdata('msg')): ?>
  <div class="alert alert-dark card-glass border-0">
    <?= html_escape($this->session->flashdata('msg')) ?>
  </div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card card-glass p-3">
      <form method="post" action="<?= site_url('admin/settings/save') ?>">
        <div class="mb-3">
          <label class="form-label">Nama Kantor</label>
          <input type="text" name="office_name" class="form-control"
                 value="<?= html_escape($row['office_name'] ?? '') ?>" required>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Latitude</label>
            <input type="text" name="lat" class="form-control"
                   value="<?= html_escape($row['lat'] ?? '') ?>" required>
            <div class="small muted mt-1">Range: -90 s/d 90</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Longitude</label>
            <input type="text" name="lng" class="form-control"
                   value="<?= html_escape($row['lng'] ?? '') ?>" required>
            <div class="small muted mt-1">Range: -180 s/d 180</div>
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-6">
            <label class="form-label">Radius Absensi (meter)</label>
            <input type="number" name="radius_m" class="form-control"
                   min="20" max="5000"
                   value="<?= html_escape($row['radius_m'] ?? 150) ?>" required>
            <div class="small muted mt-1">Rekomendasi: 80–200m untuk kantor</div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Workday Start</label>
            <input type="time" name="workday_start" class="form-control"
                   value="<?= html_escape(substr((string)($row['workday_start'] ?? '06:00:00'), 0, 5)) ?>" required>
            <div class="small muted mt-1">Hari kerja dihitung 06:00–05:59</div>
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-6">
            <label class="form-label">QR Refresh (detik)</label>
            <input type="number" name="qr_refresh_seconds" class="form-control"
                   min="5" max="120"
                   value="<?= html_escape($row['qr_refresh_seconds'] ?? 15) ?>" required>
            <div class="small muted mt-1">Monitor akan refresh token otomatis</div>
          </div>
          <div class="col-md-6">
        <label class="form-label">Token Window (detik)</label>
        <input type="number" name="token_window_seconds" class="form-control"
                min="30" max="600"
                value="<?= html_escape($row['token_window_seconds'] ?? 90) ?>" required>
        <div class="small muted mt-1">QR token dianggap valid selama rentang ini</div>
        </div>

        </div>

        <div class="d-flex gap-2 mt-3">
          <button class="btn btn-primary">Simpan Settings</button>
          <a class="btn btn-outline-light" href="<?= site_url('/') ?>">Lihat Monitor</a>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card card-glass p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="fw-semibold">QR Secret</div>
          <div class="small muted">Dipakai untuk token QR (jaga kerahasiaan).</div>
        </div>
      </div>

      <div class="mt-3">
        <textarea class="form-control" rows="3" readonly><?= html_escape($row['qr_secret'] ?? '') ?></textarea>
        <div class="small muted mt-2">
          Terakhir update: <?= html_escape($row['updated_at'] ?? '-') ?>
        </div>
      </div>

      <form method="post" action="<?= site_url('admin/settings/regenerate_secret') ?>" class="mt-3"
            onsubmit="return confirm('Generate ulang QR secret? Token lama bisa jadi invalid. Lanjut?');">
        <button class="btn btn-outline-warning w-100">Generate Ulang QR Secret</button>
      </form>

      <div class="small muted mt-3">
        Setelah regenerate, monitor akan pakai token baru otomatis. Pastikan semua perangkat scan pakai QR terbaru.
      </div>
    </div>
  </div>
</div>
