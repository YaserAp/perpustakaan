<?php
require_once __DIR__ . '/../../config/koneksi.php';

$id = $_SESSION['id'] ?? 0;

$data = mysqli_query($conn, "
SELECT buku.judul, buku.gambar, peminjaman.tanggal_pinjam, peminjaman.tanggal_kembali
FROM peminjaman 
JOIN buku ON buku.id = peminjaman.buku_id
WHERE user_id='$id' AND status='kembali'
ORDER BY peminjaman.id DESC
");
?>

<div class="page-header">
    <div class="header-title">
        <h2> Riwayat Pengembalian Buku</h2>
        <p>Arsip peminjaman buku yang telah selesai Anda kembalikan.</p>
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
                    <th>Tanggal Dikembalikan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($data) == 0):
                ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 35px; color: var(--text-muted);">
                            Belum ada riwayat pengembalian buku.
                        </td>
                    </tr>
                <?php
                else:
                    $no = 1;
                    while($d = mysqli_fetch_assoc($data)):
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <img src="assets/img/<?= htmlspecialchars($d['gambar'] ?: 'default.jpg'); ?>" 
                                 style="width: 44px; height: 58px; object-fit: cover; border-radius: 4px;"
                                 onerror="this.src='assets/img/default.jpg'">
                        </td>
                        <td><strong style="font-size: 14px;"><?= htmlspecialchars($d['judul']); ?></strong></td>
                        <td><span style="color: var(--text-muted);"><?= date('d M Y', strtotime($d['tanggal_pinjam'])); ?></span></td>
                        <td><span style="color: var(--text-muted);"><?= date('d M Y', strtotime($d['tanggal_kembali'])); ?></span></td>
                        <td>
                            <span class="badge badge-success"><i class="fa-solid fa-check"></i> Selesai Dikembalikan</span>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>
