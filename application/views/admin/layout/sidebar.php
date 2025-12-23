<?php
$uri = uri_string();
function nav_active($path, $uri){ return (strpos($uri, $path) === 0) ? 'active' : ''; }

$activeDashboard = nav_active('admin/dashboard', $uri);
$activeUsers     = nav_active('admin/users', $uri);
$activeSettings  = nav_active('admin/settings', $uri);
$activeLogs      = nav_active('admin/logs', $uri);
$activeRekapBul  = nav_active('admin/rekap_bulanan', $uri) ?: ((strpos($uri,'admin/rekap/bulanan')===0) ? 'active' : '');
?>

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

  <!-- BRAND (desktop + toggle collapse) -->
  <div class="sidebar-brand mb-4 d-none d-lg-flex justify-content-between w-100">
    <div class="d-flex align-items-center gap-2">
      <div class="brand-icon">QR</div>
      <div>
        <div class="brand-title">Absensi QR</div>
        <div class="brand-sub">Admin Panel</div>
      </div>
    </div>

    <button id="sbToggleDesk" type="button" class="btn-icon" aria-label="Minimize sidebar" title="Minimize">
      <i class="bi bi-layout-sidebar-inset"></i>
    </button>
  </div>

  <!-- ADMIN INFO -->
  <div class="sidebar-user mb-4">
    <div class="avatar"><?= strtoupper(substr($admin['name'] ?? 'A', 0, 1)) ?></div>
    <div class="w-100">
      <div class="fw-semibold text-truncate"><?= html_escape($admin['name'] ?? '-') ?></div>
      <div class="small muted text-truncate"><?= html_escape($admin['role'] ?? '-') ?></div>
    </div>
  </div>

  <!-- NAV -->
  <nav class="nav flex-column gap-1">

    <div class="nav-section">CORE</div>
    <a class="nav-link <?= $activeDashboard ?>" href="<?= site_url('admin/dashboard') ?>">
      <i class="bi bi-speedometer2"></i><span class="txt">Dashboard</span>
    </a>

    <div class="nav-section">STAFF</div>
    <a class="nav-link <?= $activeUsers ?>" href="<?= site_url('admin/users') ?>">
      <i class="bi bi-people"></i><span class="txt">Kelola Users</span>
    </a>

    <div class="nav-section">ABSENSI</div>
    <a class="nav-link <?= $activeLogs ?>" href="<?= site_url('admin/logs') ?>">
      <i class="bi bi-list-check"></i><span class="txt">Logs Absensi</span>
    </a>
    <a class="nav-link <?= $activeRekapBul ?>" href="<?= site_url('admin/rekap_bulanan') ?>">
      <i class="bi bi-calendar-month"></i><span class="txt">Rekap Bulanan</span>
    </a>

    <div class="nav-section">SYSTEM</div>
    <a class="nav-link <?= $activeSettings ?>" href="<?= site_url('admin/settings') ?>">
      <i class="bi bi-gear"></i><span class="txt">Settings Kantor</span>
    </a>

    <div class="nav-divider"></div>

    <a class="nav-link logout" href="<?= site_url('admin/logout') ?>">
      <i class="bi bi-box-arrow-right"></i><span class="txt">Logout</span>
    </a>

  </nav>
</aside>

<main class="admin-main flex-grow-1">
  <!-- TOPBAR (mobile only) -->
  <div class="admin-topbar d-lg-none">
    <div class="d-flex align-items-center justify-content-between gap-2">
      <button id="sbOpen" class="btn-icon" type="button" aria-label="Buka sidebar"><i class="bi bi-list"></i></button>
      <div class="flex-grow-1 ms-2">
        <div class="topbar-title"><?= html_escape($title ?? 'Admin Panel') ?></div>
        <div class="topbar-sub">Absensi QR • Admin</div>
      </div>
      <div class="badge-soft"><span class="dot"></span><span class="t"><?= html_escape($admin['name'] ?? 'Admin') ?></span></div>
    </div>
  </div>
  <div class="content">
