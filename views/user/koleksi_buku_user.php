<?php
require_once __DIR__ . '/../../config/koneksi.php';

$id = $_SESSION['id'] ?? 0;

$data = mysqli_query($conn, "
SELECT buku.judul, buku.gambar, buku.penulis, peminjaman.tanggal_pinjam, peminjaman.tanggal_kembali
FROM peminjaman 
JOIN buku ON buku.id = peminjaman.buku_id
WHERE user_id='$id' AND status='dipinjam'
ORDER BY peminjaman.id DESC
");
?>

<div class="page-header">
    <div class="header-title">
        <h2> Koleksi Buku Yang Sedang Dibaca</h2>
        <p>Buku-buku yang saat ini berada dalam tangan Anda.</p>
    </div>
</div>

<?php if(mysqli_num_rows($data) == 0): ?>
    <div style="text-align: center; padding: 50px 20px; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
        <i class="fa-solid fa-layer-group" style="font-size: 40px; color: var(--text-dim); margin-bottom: 12px;"></i>
        <h3 style="color: var(--text-main); margin-bottom: 6px;">Koleksi Kosong</h3>
        <p style="color: var(--text-muted); margin-bottom: 16px;">Anda belum meminjam buku apa pun saat ini.</p>
        <a href="dashboard_user.php?page=buku" class="btn btn-primary btn-sm">
            Cari & Pinjam Buku
        </a>
    </div>
<?php else: ?>
    <div class="books-grid">
        <?php while($d = mysqli_fetch_assoc($data)): ?>
        <div class="book-card">
            <div class="book-cover-wrapper">
                <img src="assets/img/<?= htmlspecialchars($d['gambar'] ?: 'default.jpg'); ?>" class="book-cover" onerror="this.src='assets/img/default.jpg'">
                <span class="stock-badge stock-available">Sedang Dibaca</span>
            </div>
            <div class="book-info">
                <div>
                    <h3 class="book-title"><?= htmlspecialchars($d['judul']); ?></h3>
                    <div class="book-author"><?= htmlspecialchars($d['penulis']); ?></div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 8px; line-height: 1.4;">
                        <div>Pinjam: <strong><?= date('d M Y', strtotime($d['tanggal_pinjam'])) ?></strong></div>
                        <div>Kembali: <strong><?= date('d M Y', strtotime($d['tanggal_kembali'])) ?></strong></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>
