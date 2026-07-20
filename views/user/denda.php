<?php
require_once __DIR__ . '/../../config/koneksi.php';

$id = $_SESSION['id'] ?? 0;
?>

<div class="page-header">
    <div class="header-title">
        <h2> Rincian Denda Saya</h2>
        <p>Informasi denda akibat keterlambatan pengembalian buku.</p>
    </div>
</div>

<!-- DENDA UNPAID -->
<div class="table-card" style="margin-bottom: 24px;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
        <h4 style="font-size: 15px; color: var(--danger);"><i class="fa-solid fa-triangle-exclamation"></i> Denda Belum Dibayar</h4>
    </div>

    <div class="table-responsive">
        <?php
        $data = mysqli_query($conn, "
        SELECT buku.judul, denda.jumlah, denda.status
        FROM denda
        JOIN peminjaman ON peminjaman.id = denda.peminjaman_id
        JOIN buku ON buku.id = peminjaman.buku_id
        WHERE peminjaman.user_id='$id' AND denda.status='belum bayar'
        ");

        if(mysqli_num_rows($data) == 0):
        ?>
            <div style="text-align: center; padding: 35px; color: var(--text-muted);">
                <i class="fa-solid fa-check-circle" style="font-size: 32px; color: var(--success); margin-bottom: 8px; display: block;"></i>
                Tidak ada denda aktif. Terima kasih telah mengembalikan buku tepat waktu! 
            </div>
        <?php else: ?>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Judul Buku</th>
                        <th>Nominal Denda</th>
                        <th>Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $total = 0;
                    while($d = mysqli_fetch_assoc($data)):
                        $total += $d['jumlah'];
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= htmlspecialchars($d['judul']); ?></strong></td>
                        <td><strong style="color: var(--danger); font-size: 14px;">Rp <?= number_format($d['jumlah']); ?></strong></td>
                        <td>
                            <span class="badge badge-danger">Belum Dibayar</span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div style="padding: 14px 20px; background: var(--danger-bg); border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <span>Total Tagihan Denda:</span>
                <h3 style="color: var(--danger); font-size: 18px;">Rp <?= number_format($total) ?></h3>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- DENDA PAID HISTORY -->
<div class="table-card">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color);">
        <h4 style="font-size: 15px;"><i class="fa-solid fa-receipt"></i> Riwayat Pembayaran Denda</h4>
    </div>

    <div class="table-responsive">
        <?php
        $data2 = mysqli_query($conn, "
        SELECT buku.judul, denda.jumlah, denda.status
        FROM denda
        JOIN peminjaman ON peminjaman.id = denda.peminjaman_id
        JOIN buku ON buku.id = peminjaman.buku_id
        WHERE peminjaman.user_id='$id' AND denda.status='sudah bayar'
        ");

        if(mysqli_num_rows($data2) == 0):
        ?>
            <div style="text-align: center; padding: 25px; color: var(--text-muted);">
                Belum ada riwayat pembayaran denda sebelumnya.
            </div>
        <?php else: ?>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Judul Buku</th>
                        <th>Nominal Denda</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while($d = mysqli_fetch_assoc($data2)):
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($d['judul']); ?></td>
                        <td>Rp <?= number_format($d['jumlah']); ?></td>
                        <td>
                            <span class="badge badge-success"><i class="fa-solid fa-check"></i> Sudah Lunas</span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
