<?php
require_once __DIR__ . '/../../config/koneksi.php';

$id = $_SESSION['id'] ?? 0;

$data = mysqli_query($conn, "
SELECT buku.judul, buku.gambar, peminjaman.id, 
       peminjaman.tanggal_pinjam, peminjaman.tanggal_kembali
FROM peminjaman 
JOIN buku ON buku.id = peminjaman.buku_id
WHERE user_id='$id' AND status='dipinjam'
ORDER BY peminjaman.id DESC
");
?>

<div class="page-header">
    <div class="header-title">
        <h2> Peminjaman Aktif Saya</h2>
        <p>Daftar buku yang sedang dalam masa peminjaman Anda.</p>
    </div>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 70px;">Cover</th>
                    <th>Judul Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Batas Kembali</th>
                    <th>Status Keterlambatan</th>
                    <th style="width: 130px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($data) == 0):
                ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            Anda sedang tidak memiliki pinjaman buku aktif. <br>
                            <a href="dashboard_user.php?page=buku" class="btn btn-primary btn-sm" style="margin-top: 12px;">
                                Pinjam Buku Sekarang
                            </a>
                        </td>
                    </tr>
                <?php
                else:
                    $no = 1;
                    $today = date('Y-m-d');
                    while($d = mysqli_fetch_assoc($data)):
                        $is_overdue = $today > $d['tanggal_kembali'];
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <img src="assets/img/<?php echo htmlspecialchars($d['gambar'] ?: 'default.jpg'); ?>" 
                                 style="width: 44px; height: 58px; object-fit: cover; border-radius: 4px;"
                                 onerror="this.src='assets/img/default.jpg'">
                        </td>
                        <td><strong style="font-size: 14px;"><?php echo htmlspecialchars($d['judul']); ?></strong></td>
                        <td><span style="color: var(--text-muted);"><?php echo date('d M Y', strtotime($d['tanggal_pinjam'])); ?></span></td>
                        <td>
                            <strong style="<?php echo $is_overdue ? 'color: var(--danger);' : 'color: var(--text-main);'; ?>">
                                <?php echo date('d M Y', strtotime($d['tanggal_kembali'])); ?>
                            </strong>
                        </td>
                        <td>
                            <?php if($is_overdue): ?>
                                <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> Terlambat (Denda Rp 2.000/hr)</span>
                            <?php else: ?>
                                <span class="badge badge-success"><i class="fa-solid fa-clock"></i> Tepat Waktu</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a class="btn btn-success btn-sm" 
                               href="kembali.php?id=<?php echo $d['id']; ?>"
                               data-judul="<?php echo htmlspecialchars($d['judul'], ENT_QUOTES); ?>"
                               onclick="return bukaKonfirmasi(this.href, 'Apakah Anda ingin mengembalikan buku ' + this.getAttribute('data-judul') + '?', 'Pengembalian Buku', 'success')">
                                <i class="fa-solid fa-rotate-left"></i> Kembalikan
                            </a>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>
