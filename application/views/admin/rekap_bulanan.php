<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-1">Rekap Bulanan</h4>
    <div class="small muted">
      Detail per hari: jam masuk (IN) dan jam pulang (OUT).
    </div>
  </div>
</div>

<!-- FILTER BULAN -->
<div class="card card-glass p-3 mb-3">
  <form method="get" action="<?= site_url('admin/rekap_bulanan') ?>">
    <div class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Bulan</label>
        <input type="month" name="month" class="form-control"
               value="<?= html_escape((string)($month ?? date('Y-m'))) ?>">
        <div class="small muted mt-1">
          Range: <?= html_escape($date_from ?? '') ?> s/d <?= html_escape($date_to ?? '') ?>
        </div>
      </div>

      <div class="col-md-4 d-flex gap-2">
        <button class="btn btn-primary w-100">Tampilkan</button>
        <a class="btn btn-outline-light w-100" href="<?= site_url('admin/rekap_bulanan') ?>">
          Bulan ini
        </a>
      </div>
    </div>
  </form>
</div>

<!-- TABLE DETAIL -->
<div class="card card-glass p-3">
  <div class="table-responsive">
    <table class="table table-dark table-borderless align-middle mb-0">
      <thead>
        <tr class="small" style="color: rgba(233,236,241,.75)">
          <th>Tanggal</th>
          <th>Pegawai</th>
          <th>Jam Masuk</th>
          <th>Jam Pulang</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($rows)): foreach ($rows as $r): ?>
          <tr>
            <!-- TANGGAL -->
            <td class="fw-semibold">
              <?= html_escape($r['workday_date']) ?>
            </td>

            <!-- PEGAWAI -->
            <td>
              <div class="fw-semibold"><?= html_escape($r['name']) ?></div>
              <div class="small text-muted"><?= html_escape($r['email']) ?></div>
            </td>

            <!-- JAM MASUK -->
            <td>
              <?php if (!empty($r['jam_masuk'])): ?>
                <span class="badge bg-success-subtle text-success border border-success">
                  <?= html_escape($r['jam_masuk']) ?>
                </span>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>

            <!-- JAM PULANG -->
            <td>
              <?php if (!empty($r['jam_pulang'])): ?>
                <span class="badge bg-primary-subtle text-primary border border-primary">
                  <?= html_escape($r['jam_pulang']) ?>
                </span>
              <?php else: ?>
                <span class="badge bg-warning-subtle text-warning border border-warning">
                  BELUM PULANG
                </span>
              <?php endif; ?>
            </td>

            <!-- STATUS -->
            <td>
              <?php if (($r['status_hari'] ?? '') === 'PULANG'): ?>
                <span class="badge bg-success">PULANG</span>
              <?php elseif (($r['status_hari'] ?? '') === 'BELUM PULANG'): ?>
                <span class="badge bg-warning text-dark">BELUM PULANG</span>
              <?php else: ?>
                <span class="badge bg-secondary">BELUM MASUK</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr>
            <td colspan="5" class="text-center text-muted py-4">
              Belum ada data rekap di bulan ini.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
