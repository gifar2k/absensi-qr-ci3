<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pegawai • Admin Absensi QR</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --bg1:#0b1220; --bg2:#0f1b33;
      --card: rgba(255,255,255,.08);
      --border: rgba(255,255,255,.14);
      --text: rgba(255,255,255,.90);
      --muted: rgba(255,255,255,.65);
    }
    body{
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      min-height: 100vh; color: var(--text);
      background:
        radial-gradient(1200px 600px at 15% 10%, rgba(125,211,252,.20), transparent 60%),
        radial-gradient(1200px 600px at 85% 10%, rgba(167,139,250,.18), transparent 55%),
        linear-gradient(180deg, var(--bg1), var(--bg2));
    }
    .glass{
      background: var(--card);
      border: 1px solid var(--border);
      box-shadow: 0 20px 50px rgba(0,0,0,.35);
      backdrop-filter: blur(14px);
      border-radius: 24px;
    }
    .muted{ color: var(--muted); }
    .btn-premium{ border-radius: 14px; padding: 10px 12px; }

    .badge-soft{
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,.18);
      background: rgba(255,255,255,.08);
      color: var(--text);
      padding: .35rem .6rem;
      font-weight: 600;
      font-size: .78rem;
      display: inline-block;
    }

    /* ==== DataTables "anti putih" ==== */
    .table-responsive{
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.10);
      border-radius: 18px;
      padding: 10px;
    }
    .table{ color: var(--text); margin:0; }
    .table thead th{ color: rgba(255,255,255,.75); border-color: rgba(255,255,255,.10); }
    .table td{ border-color: rgba(255,255,255,.10); vertical-align: middle; }

    table.dataTable,
    table.dataTable tbody,
    table.dataTable tr,
    table.dataTable td,
    table.dataTable th{
      background: transparent !important;
    }
    table.dataTable thead th{
      background: rgba(255,255,255,.06) !important;
    }
    table.dataTable tbody tr:hover td{
      background: rgba(255,255,255,.04) !important;
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select{
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.14);
      color: var(--text);
      border-radius: 12px;
      padding: .35rem .6rem;
    }
    .dataTables_wrapper .dataTables_filter label,
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_info{
      color: rgba(255,255,255,.75) !important;
    }
    .dataTables_wrapper .dataTables_paginate .page-link{
      background: rgba(255,255,255,.06);
      border-color: rgba(255,255,255,.14);
      color: var(--text);
    }
    .dataTables_wrapper .dataTables_paginate .page-item.active .page-link{
      background: rgba(59,130,246,.45);
      border-color: rgba(59,130,246,.65);
      color: #fff;
    }

    /* Modal biar nyatu */
    .modal-content{
      border-radius: 20px !important;
      background: rgba(18, 27, 46, .92);
      border: 1px solid rgba(255,255,255,.12);
      color: var(--text);
      backdrop-filter: blur(12px);
    }
    .modal-header, .modal-footer{
      border-color: rgba(255,255,255,.10) !important;
    }
    .form-control, .form-select{
      border-radius: 14px;
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.14);
      color: var(--text);
    }
    .form-control:focus, .form-select:focus{
      box-shadow:none;
      border-color: rgba(99,102,241,.55);
    }
    .btn-close{ filter: invert(1); opacity: .85; }
  </style>
</head>
<body>
  <div class="container py-4 py-md-5">
    <div class="glass p-4 p-md-5">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h3 class="mb-1">Pegawai</h3>
          <div class="muted">Kelola siapa yang boleh scan QR. Admin: <b><?= htmlspecialchars($admin_name) ?></b></div>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-outline-light btn-premium" href="<?= site_url('auth/logout') ?>">Logout</a>
          <button class="btn btn-primary btn-premium" data-bs-toggle="modal" data-bs-target="#modalAdd">+ Tambah</button>
        </div>
      </div>

      <?php if($this->session->flashdata('ok')): ?>
        <div class="alert alert-success rounded-4 mt-3"><?= $this->session->flashdata('ok') ?></div>
      <?php endif; ?>
      <?php if($this->session->flashdata('err')): ?>
        <div class="alert alert-danger rounded-4 mt-3"><?= $this->session->flashdata('err') ?></div>
      <?php endif; ?>

      <hr class="border-opacity-25 my-4">

      <div class="table-responsive">
        <table id="tbl" class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th style="width:60px;">#</th>
              <th>Nama</th>
              <th style="width:110px;">Role</th>
              <th style="width:120px;">Status</th>
              <th style="width:190px;">Dibuat</th>
              <th style="width:260px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no=1; foreach($users as $u): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td>
                  <div class="fw-semibold"><?= htmlspecialchars($u->nama) ?></div>
                  <div class="muted small">ID: <?= (int)$u->id ?></div>
                </td>
                <td>
                  <span class="badge-soft"><?= htmlspecialchars($u->role) ?></span>
                </td>
                <td>
                  <?php if((int)$u->is_active===1): ?>
                    <span class="badge-soft" style="border-color: rgba(34,197,94,.35); background: rgba(34,197,94,.12);">AKTIF</span>
                  <?php else: ?>
                    <span class="badge-soft" style="border-color: rgba(239,68,68,.35); background: rgba(239,68,68,.12);">NONAKTIF</span>
                  <?php endif; ?>
                </td>
                <td class="muted"><?= htmlspecialchars($u->created_at) ?></td>
                <td class="d-flex flex-wrap gap-2">
                  <a class="btn btn-light btn-sm btn-premium" href="<?= site_url('qr/token/'.$u->id) ?>" target="_blank">Lihat QR</a>
                  <a class="btn btn-outline-light btn-sm btn-premium" href="<?= site_url('qr/img/'.$u->id) ?>" target="_blank">QR PNG</a>
                  <a class="btn btn-outline-warning btn-sm btn-premium" href="<?= site_url('admin/pegawai/toggle/'.$u->id) ?>">
                    <?= ((int)$u->is_active===1) ? 'Nonaktifkan' : 'Aktifkan' ?>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="text-center mt-3 muted small">© Absensi QR CI3 • Admin Panel</div>
  </div>

  <!-- Modal Add -->
  <div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="post" action="<?= site_url('admin/pegawai/create') ?>">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Pegawai</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" class="form-control" name="nama" required>
            </div>
            <div class="mb-1">
              <label class="form-label">Role</label>
              <select class="form-select" name="role">
                <option value="user" selected>User</option>
                <option value="admin">Admin</option>
              </select>
              <div class="form-text text-white-50">Pegawai biasa cukup role <b>user</b>.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline-light btn-premium" type="button" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-primary btn-premium" type="submit">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

  <script>
    $(function(){
      $('#tbl').DataTable({
        pageLength: 10,
        order: [[0,'asc']],
        scrollX: true
      });
    });
  </script>
</body>
</html>
