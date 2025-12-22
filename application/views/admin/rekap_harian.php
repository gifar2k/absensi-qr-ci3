<?php $flt = $filter ?? []; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-1">Rekap Harian</h4>
    <div class="small muted">Ringkasan IN/OUT & status belum pulang per workday.</div>
  </div>

  <div class="d-flex gap-2">
    <a class="btn btn-outline-light" href="<?= site_url('admin/rekap') ?>">Reset</a>
  </div>
</div>

<div class="card card-glass p-3 mb-3">
  <form method="get" action="<?= site_url('admin/rekap') ?>">
    <div class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Dari (workday)</label>
        <input type="date" name="date_from" class="form-control"
               value="<?= html_escape((string)($flt['date_from'] ?? '')) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Sampai (workday)</label>
        <input type="date" name="date_to" class="form-control"
               value="<?= html_escape((string)($flt['date_to'] ?? '')) ?>">
      </div>
      <div class="col-md-4 d-flex gap-2">
        <button class="btn btn-primary w-100">Tampilkan</button>
        <a class="btn btn-outline-light w-100" href="<?= site_url('admin/rekap') ?>">Default</a>
      </div>
    </div>
  </form>
</div>

<div class="card card-glass p-3">
  <div class="table-responsive">
    <table class="table table-dark table-borderless align-middle mb-0">
      <thead>
        <tr class="small" style="color: rgba(233,236,241,.75)">
          <th>Workday</th>
          <th>Total Staff</th>
          <th>IN</th>
          <th>OUT</th>
          <th>Belum Masuk</th>
          <th>Belum Pulang</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($rows)): foreach ($rows as $r): ?>
          <tr>
            <td class="fw-semibold"><?= html_escape($r['workday_date']) ?></td>
            <td class="text-muted"><?= (int)$r['total_staff'] ?></td>
            <td><span class="badge bg-success-subtle text-success border border-success"><?= (int)$r['total_in'] ?></span></td>
            <td><span class="badge bg-warning-subtle text-warning border border-warning"><?= (int)$r['total_out'] ?></span></td>
            <td class="text-muted"><?= (int)$r['belum_masuk'] ?></td>
            <td>
              <?php $bp = (int)$r['belum_pulang']; ?>
              <?php if ($bp > 0): ?>
                <span class="badge bg-danger-subtle text-danger border border-danger"><?= $bp ?></span>
              <?php else: ?>
                <span class="text-muted">0</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
