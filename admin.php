<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';

if($_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$page = $_GET['page'] ?? 'dashboard';

// STATISTIK DASHBOARD
$jumlah_user = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role='user'"));
$jumlah_buku = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM buku"));
$jumlah_transaksi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman"));
$jumlah_denda_pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM denda WHERE status='belum bayar'"));

// Data untuk Chart
$dipinjam_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE status='dipinjam'"));
$dikembali_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE status='kembali'"));
?>

<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - LibrarySmart</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div>
        <div class="brand-header">
            <div class="brand-icon">
                <i class="fa-solid fa-book"></i>
            </div>
            <div class="brand-title">LibrarySmart</div>
        </div>

        <nav class="nav-menu">
            <a href="?page=dashboard" class="nav-item <?= $page == 'dashboard' ? 'active' : '' ?>" title="Dashboard">
                <i class="fa-solid fa-chart-line"></i> <span class="nav-text">Dashboard</span>
            </a>
            <a href="?page=buku" class="nav-item <?= $page == 'buku' ? 'active' : '' ?>" title="Kelola Buku">
                <i class="fa-solid fa-book"></i> <span class="nav-text">Kelola Buku</span>
            </a>
            <a href="?page=user" class="nav-item <?= $page == 'user' ? 'active' : '' ?>" title="Kelola User">
                <i class="fa-solid fa-users"></i> <span class="nav-text">Kelola User</span>
            </a>
            <a href="?page=denda" class="nav-item <?= $page == 'denda' ? 'active' : '' ?>" title="Kelola Denda">
                <i class="fa-solid fa-receipt"></i> <span class="nav-text">Kelola Denda</span>
            </a>
            <a href="?page=laporan" class="nav-item <?= $page == 'laporan' ? 'active' : '' ?>" title="Cetak Laporan">
                <i class="fa-solid fa-file-invoice"></i> <span class="nav-text">Cetak Laporan</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item logout" title="Keluar">
            <i class="fa-solid fa-right-from-bracket"></i> <span class="nav-text">Keluar</span>
        </a>
    </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main-content">

    <!-- NAVBAR -->
    <header class="top-navbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Buka / Tutup Sidebar">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>
            <button id="mobileMenuBtn" class="btn btn-secondary btn-sm" style="display: none;">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="page-title">
                <h3>Admin Panel</h3>
            </div>
        </div>

        <div class="navbar-right">
            <button class="theme-toggle-btn" id="themeToggleBtn" title="Ganti Tema">
                <i class="fa-solid fa-moon"></i>
            </button>

            <div class="user-profile-badge">
                <div class="avatar">
                    <?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></span>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTAINER -->
    <div class="page-container">

<?php if($page == 'dashboard'): ?>

    <div class="page-header">
        <div class="header-title">
            <h2>Ringkasan Dashboard</h2>
            <p>Ikhtisar statistik dan aktivitas terkini di sistem perpustakaan.</p>
        </div>
    </div>

    <!-- CARDS GRID -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-top">
                <div class="stat-val"><?= $jumlah_user ?></div>
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="stat-label">Total Anggota Active</div>
        </div>

        <div class="stat-card success">
            <div class="stat-top">
                <div class="stat-val"><?= $jumlah_buku ?></div>
                <div class="stat-icon"><i class="fa-solid fa-book"></i></div>
            </div>
            <div class="stat-label">Koleksi Buku</div>
        </div>

        <div class="stat-card warning">
            <div class="stat-top">
                <div class="stat-val"><?= $jumlah_transaksi ?></div>
                <div class="stat-icon"><i class="fa-solid fa-hand-holding"></i></div>
            </div>
            <div class="stat-label">Total Transaksi</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-top">
                <div class="stat-val"><?= $jumlah_denda_pending ?></div>
                <div class="stat-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
            </div>
            <div class="stat-label">Denda Belum Lunas</div>
        </div>
    </div>

    <!-- CHARTS & RECENT ACTIVITY -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 24px;">
        
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 20px;">
            <h4 style="margin-bottom: 16px; font-size: 15px;">Status Peminjaman Buku</h4>
            <div style="height: 220px; position: relative;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 20px;">
            <h4 style="margin-bottom: 16px; font-size: 15px;">Peminjaman Terbaru</h4>
            <div class="table-responsive">
                <table class="custom-table" style="font-size: 13px;">
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Judul Buku</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent = mysqli_query($conn, "
                            SELECT users.nama, buku.judul, peminjaman.status 
                            FROM peminjaman 
                            JOIN users ON users.id = peminjaman.user_id 
                            JOIN buku ON buku.id = peminjaman.buku_id 
                            ORDER BY peminjaman.id DESC LIMIT 5
                        ");
                        if(mysqli_num_rows($recent) == 0):
                        ?>
                            <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">Belum ada aktivitas.</td></tr>
                        <?php else:
                            while($r = mysqli_fetch_assoc($recent)):
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($r['nama']) ?></strong></td>
                                <td><?= htmlspecialchars($r['judul']) ?></td>
                                <td>
                                    <?php if($r['status'] == 'dipinjam'): ?>
                                        <span class="badge badge-warning">Dipinjam</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Dikembalikan</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('statusChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sedang Dipinjam', 'Sudah Dikembalikan'],
                    datasets: [{
                        data: [<?= $dipinjam_count ?>, <?= $dikembali_count ?>],
                        backgroundColor: ['#d97706', '#16a34a'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { font: { family: 'Inter', size: 12 } }
                        }
                    }
                }
            });
        });
    </script>

<?php endif; ?>

<?php
if($page == 'buku') include __DIR__ . '/views/admin/buku_admin.php';
if($page == 'user') include __DIR__ . '/views/admin/user_admin.php';
if($page == 'denda') include __DIR__ . '/views/admin/denda_admin.php';
if($page == 'laporan') include __DIR__ . '/views/admin/laporan_admin.php';
?>

    </div>
</main>

<?php include __DIR__ . '/views/confirm_modal.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>