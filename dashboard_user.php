<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$page = $_GET['page'] ?? 'home';

// User Stats
$buku_dipinjam = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE user_id='$user_id' AND status='dipinjam'"));
$buku_kembali = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE user_id='$user_id' AND status='kembali'"));

$denda_query = mysqli_query($conn, "
    SELECT SUM(jumlah) as total 
    FROM denda 
    JOIN peminjaman ON peminjaman.id = denda.peminjaman_id 
    WHERE peminjaman.user_id='$user_id' AND denda.status='belum bayar'
");
$denda_data = mysqli_fetch_assoc($denda_query);
$total_denda = $denda_data['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - LibrarySmart</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <a href="?page=home" class="nav-item <?= $page == 'home' ? 'active' : '' ?>" title="Dashboard">
                <i class="fa-solid fa-house"></i> <span class="nav-text">Dashboard</span>
            </a>
            <a href="?page=buku" class="nav-item <?= $page == 'buku' ? 'active' : '' ?>" title="Cari Buku">
                <i class="fa-solid fa-magnifying-glass"></i> <span class="nav-text">Cari Buku</span>
            </a>
            <a href="?page=peminjaman" class="nav-item <?= $page == 'peminjaman' ? 'active' : '' ?>" title="Pinjaman Aktif">
                <i class="fa-solid fa-book-bookmark"></i> <span class="nav-text">Pinjaman Aktif</span>
                <?php if($buku_dipinjam > 0): ?>
                    <span class="badge badge-warning badge-count" style="margin-left: auto; padding: 2px 6px; font-size: 11px;"><?= $buku_dipinjam ?></span>
                <?php endif; ?>
            </a>
            <a href="?page=pengembalian" class="nav-item <?= $page == 'pengembalian' ? 'active' : '' ?>" title="Riwayat Kembali">
                <i class="fa-solid fa-rotate-left"></i> <span class="nav-text">Riwayat Kembali</span>
            </a>
            <a href="?page=denda" class="nav-item <?= $page == 'denda' ? 'active' : '' ?>" title="Denda Saya">
                <i class="fa-solid fa-wallet"></i> <span class="nav-text">Denda Saya</span>
                <?php if($total_denda > 0): ?>
                    <span class="badge badge-danger badge-count" style="margin-left: auto; padding: 2px 6px; font-size: 11px;">!</span>
                <?php endif; ?>
            </a>
            <a href="?page=koleksi" class="nav-item <?= $page == 'koleksi' ? 'active' : '' ?>" title="Koleksi Saya">
                <i class="fa-solid fa-layer-group"></i> <span class="nav-text">Koleksi Saya</span>
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
                <h3>Portal Anggota</h3>
            </div>
        </div>

        <div class="navbar-right">
            <button type="button" class="btn btn-primary btn-sm" onclick="bukaModalScanner()" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px;">
                <i class="fa-solid fa-qrcode"></i> <span>Scan QR</span>
            </button>

            <button class="theme-toggle-btn" id="themeToggleBtn" title="Ganti Tema">
                <i class="fa-solid fa-moon"></i>
            </button>

            <div class="user-profile-badge">
                <div class="avatar">
                    <?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></span>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTAINER -->
    <div class="page-container">

<?php if ($page == 'home'): ?>

    <!-- HEADER BLOCK -->
    <div class="page-header">
        <div class="header-title">
            <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?></h2>
            <p>Kelola peminjaman buku dan temukan literatur perpustakaan dengan mudah.</p>
        </div>
        <a href="?page=buku" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass"></i> Cari Katalog Buku
        </a>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card warning">
            <div class="stat-top">
                <div class="stat-val"><?= $buku_dipinjam ?></div>
                <div class="stat-icon"><i class="fa-solid fa-book-bookmark"></i></div>
            </div>
            <div class="stat-label">Buku Sedang Dipinjam</div>
        </div>

        <div class="stat-card success">
            <div class="stat-top">
                <div class="stat-val"><?= $buku_kembali ?></div>
                <div class="stat-icon"><i class="fa-solid fa-check"></i></div>
            </div>
            <div class="stat-label">Buku Selesai Dikembalikan</div>
        </div>

        <div class="stat-card danger">
            <div class="stat-top">
                <div class="stat-val">Rp <?= number_format($total_denda) ?></div>
                <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
            </div>
            <div class="stat-label">Denda Belum Dibayar</div>
        </div>
    </div>

    <!-- RECOMMENDED BOOKS -->
    <div class="page-header" style="margin-top: 10px;">
        <div class="header-title">
            <h2>Koleksi Buku Terbaru</h2>
            <p>Buku yang baru ditambahkan ke katalog perpustakaan.</p>
        </div>
        <a href="?page=buku" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>

    <div class="books-grid">
        <?php
        $data = mysqli_query($conn, "SELECT * FROM buku ORDER BY id DESC LIMIT 8");
        while($b = mysqli_fetch_assoc($data)):
        ?>
        <div class="book-card">
            <div class="book-cover-wrapper">
                <img src="assets/img/<?= htmlspecialchars($b['gambar'] ?: 'default.jpg') ?>" class="book-cover" onerror="this.src='assets/img/default.jpg'">
                <?php if($b['stok'] > 0): ?>
                    <span class="stock-badge stock-available">Tersedia (<?= $b['stok'] ?>)</span>
                <?php else: ?>
                    <span class="stock-badge stock-empty">Habis</span>
                <?php endif; ?>
            </div>
            <div class="book-info">
                <div>
                    <h3 class="book-title"><?= htmlspecialchars($b['judul']) ?></h3>
                    <div class="book-author"><?= htmlspecialchars($b['penulis']) ?></div>
                </div>
                <?php if($b['stok'] > 0): ?>
                    <button class="btn btn-primary btn-sm" style="width: 100%; margin-top: 8px;"
                        onclick="bukaModalPinjam('<?= $b['id'] ?>', '<?= htmlspecialchars(addslashes($b['judul'])) ?>', '<?= htmlspecialchars(addslashes($b['penulis'])) ?>')">
                        Pinjam Buku
                    </button>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm" style="width: 100%; margin-top: 8px; opacity: 0.6;" disabled>
                        Stok Habis
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

<?php endif; ?>

<?php
if ($page == 'buku') include __DIR__ . '/views/user/buku.php';
if ($page == 'peminjaman') include __DIR__ . '/views/user/peminjaman.php';
if ($page == 'pengembalian') include __DIR__ . '/views/user/pengembalian.php';
if ($page == 'denda') include __DIR__ . '/views/user/denda.php';
if ($page == 'koleksi') include __DIR__ . '/views/user/koleksi_buku_user.php';
?>

    </div>
</main>

<!-- MODAL PINJAM -->
<div id="modalPinjam" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Pinjam Buku</h3>
            <button class="modal-close" onclick="closeModal('modalPinjam')">&times;</button>
        </div>

        <form method="POST" action="pinjam.php">
            <input type="hidden" name="buku_id" id="modal_buku_id">
            <input type="hidden" name="redirect_page" value="<?= htmlspecialchars($page) ?>">

            <div style="background: var(--bg-main); padding: 12px 14px; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 16px;">
                <div id="modal_judul_buku" style="font-weight: 600; font-size: 14px;"></div>
                <div id="modal_penulis_buku" style="color: var(--text-muted); font-size: 12.5px; margin-top: 2px;"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Durasi Peminjaman (Hari)</label>
                <input type="number" name="lama" class="form-control" value="7" min="1" max="30" required>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="closeModal('modalPinjam')">Batal</button>
                <button type="submit" name="pinjam" class="btn btn-primary" style="flex: 1;">Konfirmasi Pinjam</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL BERI ULASAN BUKU -->
<div id="modalUlasan" class="modal-overlay">
    <div class="modal-content" style="max-width: 440px;">
        <div class="modal-header">
            <h3><i class="fa-solid fa-star" style="color: var(--warning);"></i> Beri Ulasan Buku</h3>
            <button class="modal-close" onclick="closeModal('modalUlasan')">&times;</button>
        </div>
        <form action="simpan_ulasan.php" method="POST" style="margin-top: 14px;">
            <input type="hidden" name="buku_id" id="ulasan_buku_id">
            
            <div style="background: var(--bg-main); padding: 12px; border-radius: 8px; margin-bottom: 14px; border: 1px solid var(--border-color);">
                <strong id="ulasan_judul_buku" style="font-size: 14px; color: var(--text-main);">Judul Buku</strong>
            </div>

            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label">Pilih Rating (Bintang):</label>
                <select name="rating" class="form-control" style="font-size: 14px;">
                    <option value="5">⭐⭐⭐⭐⭐ (5/5) Sangat Bagus</option>
                    <option value="4">⭐⭐⭐⭐ (4/5) Bagus</option>
                    <option value="3">⭐⭐⭐ (3/5) Cukup</option>
                    <option value="2">⭐⭐ (2/5) Kurang</option>
                    <option value="1">⭐ (1/5) Buruk</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 18px;">
                <label class="form-label">Tulis Ulasan & Kesan Kamu:</label>
                <textarea name="komentar" class="form-control" rows="3" placeholder="Bagaimana menurutmu buku ini? Berikan ulasan singkat..." required></textarea>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="closeModal('modalUlasan')">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex: 1.2; justify-content: center;">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Ulasan
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/views/qr_scanner_modal.php'; ?>
<?php include __DIR__ . '/views/confirm_modal.php'; ?>
<script src="assets/js/main.js"></script>
<script>
function bukaModalPinjam(id, judul, penulis) {
    document.getElementById('modal_buku_id').value = id;
    document.getElementById('modal_judul_buku').innerText = judul;
    document.getElementById('modal_penulis_buku').innerText = "Penulis: " + penulis;
    openModal('modalPinjam');
}

function bukaModalUlasan(id, judul) {
    document.getElementById('ulasan_buku_id').value = id;
    document.getElementById('ulasan_judul_buku').innerText = judul;
    openModal('modalUlasan');
}
</script>
</body>
</html>
