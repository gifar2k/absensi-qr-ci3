<?php
$flt = $filter ?? [];
$exportUrl = site_url('admin/logs/export')
    . '?date_from=' . rawurlencode((string)($flt['date_from'] ?? ''))
    . '&date_to=' . rawurlencode((string)($flt['date_to'] ?? ''))
    . '&user_id=' . rawurlencode((string)($flt['user_id'] ?? ''))
    . '&status=' . rawurlencode((string)($flt['status'] ?? ''))
    . '&q=' . rawurlencode((string)($flt['q'] ?? ''));
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-1">Attendance Logs</h4>
    <div class="small muted">
      Total hasil: <?= (int)($total ?? 0) ?> —
      IN: <?= (int)($summary['in'] ?? 0) ?> • OUT: <?= (int)($summary['out'] ?? 0) ?>
    </div>
  </div>

  <div class="d-flex gap-2">
    <a href="<?= $exportUrl ?>" class="btn btn-outline-light">Export CSV</a>
  </div>
</div>

<div class="card card-glass p-3 mb-3">
  <form method="get" action="<?= site_url('admin/logs') ?>">
    <div class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Dari (workday)</label>
        <input type="date" name="date_from" class="form-control"
               value="<?= html_escape((string)($flt['date_from'] ?? '')) ?>">
      </div>

      <div class="col-md-3">
        <label class="form-label">Sampai (workday)</label>
        <input type="date" name="date_to" class="form-control"
               value="<?= html_escape((string)($flt['date_to'] ?? '')) ?>">
      </div>

      <div class="col-md-3">
        <label class="form-label">Pegawai</label>
        <select name="user_id" class="form-control">
          <option value="0">Semua</option>
          <?php foreach (($staff ?? []) as $u): ?>
            <option value="<?= (int)$u['id'] ?>"
              <?= ((int)($flt['user_id'] ?? 0) === (int)$u['id']) ? 'selected' : '' ?>>
              <?= html_escape($u['name'].' — '.$u['email']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <option value="">Semua</option>
          <option value="IN"  <?= (strtoupper((string)($flt['status'] ?? '')) === 'IN') ? 'selected' : '' ?>>IN (MASUK)</option>
          <option value="OUT" <?= (strtoupper((string)($flt['status'] ?? '')) === 'OUT') ? 'selected' : '' ?>>OUT (PULANG)</option>
        </select>
      </div>

      <div class="col-md-9">
        <label class="form-label">Cari (nama/email)</label>
        <input type="text" name="q" class="form-control"
               placeholder="contoh: rudi / @ilogcomputer.com"
               value="<?= html_escape((string)($flt['q'] ?? '')) ?>">
      </div>

      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-primary w-100">Filter</button>
        <a class="btn btn-outline-light w-100" href="<?= site_url('admin/logs') ?>">Reset</a>
      </div>
    </div>
  </form>

  <div class="small muted mt-2">
    Catatan: Filter memakai <b>workday_date</b> (mengikuti Workday Start kantor).
  </div>
</div>

<div class="card card-glass p-3">
  <div class="table-responsive">
    <table class="table table-dark table-borderless align-middle mb-0">
      <thead>
        <tr class="small" style="color: rgba(233,236,241,.75)">
          <th>Workday</th>
          <th>Waktu</th>
          <th>Pegawai</th>
          <th>Status</th>
          <th>Distance</th>
          <th>Accuracy</th>
          <th class="text-end">Meta</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($rows)): foreach ($rows as $r): ?>
          <tr>
            <td class="text-muted"><?= html_escape($r['workday_date'] ?? '-') ?></td>
            <td class="fw-semibold"><?= html_escape($r['taken_at'] ?? '-') ?></td>

            <td>
              <div class="fw-semibold"><?= html_escape($r['name'] ?? '-') ?></div>
              <div class="small text-muted"><?= html_escape($r['email'] ?? '-') ?></div>
            </td>

            <td>
              <?php if (($r['status'] ?? '') === 'IN'): ?>
                <span class="badge bg-success-subtle text-success border border-success">IN</span>
              <?php else: ?>
                <span class="badge bg-warning-subtle text-warning border border-warning">OUT</span>
              <?php endif; ?>
            </td>

            <td class="text-muted"><?= (int)($r['distance_m'] ?? 0) ?> m</td>
            <td class="text-muted"><?= html_escape((string)($r['accuracy_m'] ?? '-')) ?></td>

            <td class="text-end">
              <div class="small text-muted">
                src: <?= html_escape($r['source'] ?? '-') ?>
                <span class="mx-2">•</span>
                lat: <?= html_escape((string)($r['lat'] ?? '-')) ?>
                <span class="mx-2">•</span>
                lng: <?= html_escape((string)($r['lng'] ?? '-')) ?>
              </div>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr>
            <td colspan="7" class="text-center text-muted py-4">Tidak ada data sesuai filter.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
