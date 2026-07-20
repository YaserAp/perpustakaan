<?php
require_once __DIR__ . '/../../config/koneksi.php';

$user_id = $_SESSION['id'] ?? 0;
$search = $_GET['search'] ?? '';

// Process Pinjam
if(isset($_POST['pinjam'])){
    $buku_id = (int)$_POST['buku_id'];
    $lama = (int)$_POST['lama'];
    $redirect_page = $_POST['redirect_page'] ?? 'buku';

    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali = date('Y-m-d', strtotime("+$lama days"));

    $cek_stok = mysqli_query($conn, "SELECT stok FROM buku WHERE id='$buku_id'");
    $stok_data = mysqli_fetch_assoc($cek_stok);

    if($stok_data && $stok_data['stok'] > 0){
        mysqli_query($conn, "
        INSERT INTO peminjaman (user_id, buku_id, tanggal_pinjam, tanggal_kembali, status)
        VALUES ('$user_id','$buku_id','$tgl_pinjam','$tgl_kembali','dipinjam')
        ");

        mysqli_query($conn, "
        UPDATE buku SET stok = stok - 1 WHERE id='$buku_id'
        ");

        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                showToast('<i class=\"fa-solid fa-circle-check\"></i> Buku berhasil dipinjam!', 'success');
                setTimeout(() => { location.href='dashboard_user.php?page=peminjaman'; }, 1200);
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                showToast('<i class=\"fa-solid fa-circle-xmark\"></i> Maaf, stok buku telah habis!', 'danger');
            });
        </script>";
    }
}
?>

<div class="page-header">
    <div class="header-title">
        <h2> Pencarian Katalog Buku</h2>
        <p>Temukan literatur, majalah, dan buku referensi yang kamu butuhkan.</p>
    </div>
</div>

<div class="table-card" style="padding: 16px 20px; margin-bottom: 24px;">
    <form method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <input type="hidden" name="page" value="buku">
        <div class="search-box" style="flex: 1; max-width: none;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" id="tableFilterInput" class="form-control" 
                   placeholder="Cari kata kunci judul buku atau nama penulis..." 
                   value="<?= htmlspecialchars($search); ?>">
        </div>
        <button type="submit" class="btn btn-primary" style="height: 38px;">
            <i class="fa-solid fa-magnifying-glass"></i> Cari
        </button>
        <?php if($search): ?>
            <a href="dashboard_user.php?page=buku" class="btn btn-secondary" style="height: 38px;">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        <?php endif; ?>
    </form>
</div>

<div class="books-grid">
<?php
if($search){
    $search_safe = mysqli_real_escape_string($conn, $search);
    $query = "SELECT * FROM buku 
              WHERE judul LIKE '%$search_safe%' 
              OR penulis LIKE '%$search_safe%' 
              ORDER BY id DESC";
} else {
    $query = "SELECT * FROM buku ORDER BY id DESC";
}

$data = mysqli_query($conn, $query);

if(mysqli_num_rows($data) == 0):
?>
    <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
        <i class="fa-solid fa-book-open" style="font-size: 40px; color: var(--text-dim); margin-bottom: 12px;"></i>
        <h3 style="color: var(--text-main); margin-bottom: 6px;">Buku Tidak Ditemukan</h3>
        <p style="color: var(--text-muted);">Coba ubah kata kunci pencarian atau reset filter untuk melihat katalog penuh.</p>
    </div>
<?php
else:
    while($b = mysqli_fetch_assoc($data)):
        $buku_id_loop = $b['id'];
        $u_query = mysqli_query($conn, "SELECT AVG(rating) as avg_rating, COUNT(*) as total_review FROM ulasan WHERE buku_id='$buku_id_loop'");
        $u_data = mysqli_fetch_assoc($u_query);
        $avg_rating = round($u_data['avg_rating'] ?: 5.0, 1);
        $total_review = (int)$u_data['total_review'];
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

                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; font-size: 12px; color: var(--warning);">
                    <div>
                        <i class="fa-solid fa-star"></i> <strong><?= number_format($avg_rating, 1) ?></strong>
                        <span style="color: var(--text-muted); font-size: 11px;">(<?= $total_review ?> ulasan)</span>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" style="padding: 2px 8px; font-size: 11px;"
                            onclick="bukaModalUlasan('<?= $b['id'] ?>', '<?= htmlspecialchars(addslashes($b['judul'])) ?>')">
                        <i class="fa-solid fa-pen"></i> Ulas
                    </button>
                </div>
            </div>
            <?php if($b['stok'] > 0): ?>
                <button class="btn btn-primary btn-sm" style="width: 100%; margin-top: 10px;"
                        onclick="bukaModalPinjam('<?= $b['id'] ?>', '<?= htmlspecialchars(addslashes($b['judul'])) ?>', '<?= htmlspecialchars(addslashes($b['penulis'])) ?>')">
                    Pinjam Buku
                </button>
            <?php else: ?>
                <button class="btn btn-secondary btn-sm" style="width: 100%; margin-top: 10px; opacity: 0.6;" disabled>
                    Stok Habis
                </button>
            <?php endif; ?>
        </div>
    </div>
<?php endwhile; endif; ?>
</div>
