<?php
$uri = uri_string();

// aktif kalau prefix match
function nav_active($path, $uri) {
  return (strpos($uri, $path) === 0) ? 'active' : '';
}

$activeDashboard = nav_active('admin/dashboard', $uri);
$activeUsers     = nav_active('admin/users', $uri);
$activeSettings  = nav_active('admin/settings', $uri);
$activeLogs      = nav_active('admin/logs', $uri);
$activeRekap     = nav_active('admin/rekap', $uri);
$activeRekapBul  = nav_active('admin/rekap_bulanan', $uri) ?: (strpos($uri,'admin/rekap/bulanan')===0 ? 'active' : '');
?>

<!-- OVERLAY (muncul hanya di mobile saat sidebar open) -->
<div id="sbOverlay" class="sb-overlay d-lg-none" aria-hidden="true"></div>

<aside id="adminSidebar" class="admin-sidebar p-3" aria-label="Sidebar">

  <!-- HEADER SIDEBAR (mobile) -->
  <div class="d-flex align-items-center justify-content-between d-lg-none mb-3">
    <div class="sidebar-brand">
      <div class="brand-icon">QR</div>
      <div>
        <div class="brand-title">Absensi QR</div>
        <div class="brand-sub">Admin Panel</div>
      </div>
    </div>

    <button type="button" class="btn btn-sm btn-outline-light" id="sbClose" aria-label="Tutup sidebar">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <!-- BRAND (desktop) -->
  <div class="sidebar-brand mb-4 d-none d-lg-flex">
    <div class="brand-icon">QR</div>
    <div>
      <div class="brand-title">Absensi QR</div>
      <div class="brand-sub">Admin Panel</div>
    </div>
  </div>

  <!-- ADMIN INFO -->
  <div class="sidebar-user mb-4">
    <div class="avatar">
      <?= strtoupper(substr($admin['name'] ?? 'A', 0, 1)) ?>
    </div>
    <div class="w-100">
      <div class="fw-semibold text-truncate"><?= html_escape($admin['name'] ?? '-') ?></div>
      <div class="small muted text-truncate"><?= html_escape($admin['role'] ?? '-') ?></div>
    </div>
  </div>

  <!-- NAV -->
  <nav class="nav flex-column gap-1">

    <div class="nav-section">CORE</div>
    <a class="nav-link <?= $activeDashboard ?>" href="<?= site_url('admin/dashboard') ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-section">PEGAWAI</div>
    <a class="nav-link <?= $activeUsers ?>" href="<?= site_url('admin/users') ?>">
      <i class="bi bi-people"></i> Kelola Users
    </a>

    <div class="nav-section">ABSENSI</div>
    <a class="nav-link <?= $activeLogs ?>" href="<?= site_url('admin/logs') ?>">
      <i class="bi bi-list-check"></i> Logs Absensi
    </a>
    <a class="nav-link <?= $activeRekapBul ?>" href="<?= site_url('admin/rekap_bulanan') ?>">
      <i class="bi bi-calendar-month"></i> Rekap Bulanan
    </a>

    <div class="nav-section">SYSTEM</div>
    <a class="nav-link <?= $activeSettings ?>" href="<?= site_url('admin/settings') ?>">
      <i class="bi bi-gear"></i> Settings Kantor
    </a>

    <div class="nav-divider"></div>

    <a class="nav-link logout" href="<?= site_url('admin/logout') ?>">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>

  </nav>
</aside>

<main class="admin-main flex-grow-1">
  <div class="content p-4">
