<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-1">Rekap Bulanan</h4>
    <div class="small muted">Per pegawai: total hari MASUK, PULANG, dan belum pulang.</div>
  </div>
</div>

<div class="card card-glass p-3 mb-3">
  <form method="get" action="<?= site_url('admin/rekap/bulanan') ?>">
    <div class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Bulan</label>
        <input type="month" name="month" class="form-control"
               value="<?= html_escape((string)($month ?? date('Y-m'))) ?>">
        <div class="small muted mt-1">Range: <?= html_escape($date_from ?? '') ?> s/d <?= html_escape($date_to ?? '') ?></div>
      </div>

      <div class="col-md-4 d-flex gap-2">
        <button class="btn btn-primary w-100">Tampilkan</button>
        <a class="btn btn-outline-light w-100" href="<?= site_url('admin/rekap/bulanan') ?>">Bulan ini</a>
      </div>
    </div>
  </form>
</div>

<div class="card card-glass p-3">
  <div class="table-responsive">
    <table class="table table-dark table-borderless align-middle mb-0">
      <thead>
        <tr class="small" style="color: rgba(233,236,241,.75)">
          <th>Pegawai</th>
          <th>Hadir (IN)</th>
          <th>Pulang (OUT)</th>
          <th>Belum Pulang</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($rows)): foreach ($rows as $r): ?>
          <tr>
            <td>
              <div class="fw-semibold"><?= html_escape($r['name']) ?></div>
              <div class="small text-muted"><?= html_escape($r['email']) ?></div>
            </td>
            <td><span class="badge bg-success-subtle text-success border border-success"><?= (int)$r['hadir_hari'] ?></span></td>
            <td><span class="badge bg-warning-subtle text-warning border border-warning"><?= (int)$r['pulang_hari'] ?></span></td>
            <td>
              <?php $bp = (int)$r['belum_pulang_hari']; ?>
              <?php if ($bp > 0): ?>
                <span class="badge bg-danger-subtle text-danger border border-danger"><?= $bp ?></span>
              <?php else: ?>
                <span class="text-muted">0</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
