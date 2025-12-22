<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-1">Kelola Users</h4>
    <div class="small muted">Pegawai aktif/nonaktif, device lock, & reset device</div>
  </div>

  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUser">
    + Tambah Pegawai
  </button>
</div>

<div class="card card-glass p-3">
  <div class="table-responsive">
    <table class="table table-dark table-borderless align-middle mb-0">
      <thead>
        <tr class="small" style="color: rgba(233,236,241,.75)">
          <th>Nama</th>
          <th>Email</th>
          <th>Status</th>
          <th>Device Status</th>
          <th>Device Lock</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($rows as $r):
          $uid = (int)$r['id'];
          $isActive = (int)$r['is_active'] === 1;
          $dev = $device_map[$uid] ?? null;
          $isLocked = $dev ? 1 : 0;
        ?>
          <tr>
            <td class="fw-semibold"><?= html_escape($r['name']) ?></td>
            <td class="text-muted"><?= html_escape($r['email']) ?></td>

            <td>
              <?= $isActive
                ? '<span class="badge bg-success-subtle text-success border border-success">Aktif</span>'
                : '<span class="badge bg-danger-subtle text-danger border border-danger">Nonaktif</span>' ?>
            </td>

            <td>
              <?= $isLocked
                ? '<span class="badge bg-info-subtle text-info border border-info">Locked</span>'
                : '<span class="badge bg-secondary-subtle text-secondary border border-secondary">Unlocked</span>' ?>
            </td>

            <td>
              <?php if ($dev): ?>
                <div class="small text-muted">
                  <?= html_escape($dev['device_label'] ?? 'Device aktif') ?><br>
                  Last: <?= html_escape($dev['last_seen_at'] ?? '-') ?>
                </div>
              <?php else: ?>
                <span class="small text-muted">Belum ada device</span>
              <?php endif; ?>
            </td>

            <td class="text-end">
              <button
                class="btn btn-sm btn-outline-light btn-edit-user"
                data-id="<?= (int)$uid ?>"
                data-name="<?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?>"
                data-email="<?= htmlspecialchars($r['email'], ENT_QUOTES, 'UTF-8') ?>"
                data-active="<?= (int)$isActive ?>"
                data-bs-toggle="modal"
                data-bs-target="#modalEditUser">
                Edit
              </button>

              <a href="<?= site_url('admin/users/deactivate/'.$uid) ?>"
                 class="btn btn-sm btn-outline-danger ms-1"
                 onclick="return confirm('Nonaktifkan pegawai ini?');">
                Delete
              </a>

              <?php if ($dev): ?>
                <a href="<?= site_url('admin/users/reset_device/'.$uid) ?>"
                   class="btn btn-sm btn-outline-warning ms-1"
                   onclick="return confirm('Reset device lock user ini?');">
                  Reset Device
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if (empty($rows)): ?>
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Belum ada pegawai.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Add Pegawai -->
<div class="modal fade" id="modalAddUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content card-glass p-2">
      <div class="modal-header border-0">
        <h5 class="modal-title">Tambah Pegawai</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="post" action="<?= site_url('admin/users/create') ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Status</label>
            <select name="is_active" class="form-control">
              <option value="1" selected>Aktif</option>
              <option value="0">Nonaktif</option>
            </select>
          </div>

          <div class="small muted mt-2">
            Pegawai tidak punya password. Mereka absen pakai email saat scan QR.
          </div>
        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Pegawai -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content card-glass p-2">
      <div class="modal-header border-0">
        <h5 class="modal-title">Edit Pegawai</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form method="post" id="formEditUser" action="">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="edit_email" class="form-control" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Status</label>
            <select name="is_active" id="edit_active" class="form-control">
              <option value="1">Aktif</option>
              <option value="0">Nonaktif</option>
            </select>
          </div>

          <div class="small muted mt-2">Perubahan email mempengaruhi validasi absensi.</div>
        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.btn-edit-user').forEach(btn => {
  btn.addEventListener('click', function () {
    const id = this.dataset.id;

    document.getElementById('edit_name').value   = this.dataset.name || '';
    document.getElementById('edit_email').value  = this.dataset.email || '';
    document.getElementById('edit_active').value = String(this.dataset.active ?? '1');

    const base = "<?= rtrim(site_url('admin/users/update'), '/') ?>";
    document.getElementById('formEditUser').action = base + '/' + id;
  });
});
</script>

<?php $toast = $this->session->flashdata('toast'); ?>
<?php if (!empty($toast) && isset($toast['ok'], $toast['msg'])): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
  Swal.fire({
    icon: <?= $toast['ok'] ? "'success'" : "'error'" ?>,
    title: <?= $toast['ok'] ? "'Berhasil'" : "'Gagal'" ?>,
    text: <?= json_encode((string)$toast['msg']) ?>,
    timer: 1800,
    showConfirmButton: false
  });
});
</script>
<?php endif; ?>
