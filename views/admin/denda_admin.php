<?php
require_once __DIR__ . '/../../config/koneksi.php';

$email = $_GET['email'] ?? '';
$email = mysqli_real_escape_string($conn, $email);
?>

<div class="page-header">
    <div class="header-title">
        <h2> Kelola & Cek Denda</h2>
        <p>Cek dan konfirmasi pembayaran denda keterlambatan pengembalian buku.</p>
    </div>
</div>

<div class="table-card" style="margin-bottom: 20px; padding: 16px 20px;">
    <form method="GET" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
        <input type="hidden" name="page" value="denda">
        <div style="flex: 1; min-width: 220px;">
            <label class="form-label"><i class="fa-solid fa-envelope"></i> Cari Email User</label>
            <input type="text" name="email" class="form-control" placeholder="Masukkan alamat email user..." value="<?= htmlspecialchars($email) ?>">
        </div>
        <button type="submit" class="btn btn-primary" style="height: 38px;">
            <i class="fa-solid fa-magnifying-glass"></i> Cari Denda
        </button>
        <?php if(!empty($email)): ?>
            <a href="admin.php?page=denda" class="btn btn-secondary" style="height: 38px;">
                <i class="fa-solid fa-rotate-left"></i> Reset Filter
            </a>
        <?php endif; ?>
    </form>
</div>

<div class="table-card">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <h4 style="font-size: 15px;"><i class="fa-solid fa-receipt"></i> Daftar Denda Pengguna</h4>
        <?php if(!empty($email)): ?>
            <span class="badge badge-info">Email: <?= htmlspecialchars($email) ?></span>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama User</th>
                    <th>Email</th>
                    <th>Judul Buku</th>
                    <th>Nominal Denda</th>
                    <th>Status</th>
                    <th style="width: 130px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "
                SELECT 
                    denda.id,
                    users.nama, 
                    users.email, 
                    buku.judul, 
                    denda.jumlah, 
                    denda.status
                FROM denda
                JOIN peminjaman ON peminjaman.id = denda.peminjaman_id
                JOIN users ON users.id = peminjaman.user_id
                JOIN buku ON buku.id = peminjaman.buku_id
                ";

                if(!empty($email)){
                    $sql .= " WHERE users.email='$email'";
                }

                $sql .= " ORDER BY denda.id DESC";

                $data = mysqli_query($conn, $sql);

                if(mysqli_num_rows($data) == 0):
                ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: var(--text-muted);">
                            <?= !empty($email) ? 'Tidak ada denda yang ditemukan untuk email ini.' : 'Belum ada denda tercatat.' ?>
                        </td>
                    </tr>
                <?php 
                else:
                    $no = 1;
                    while($d = mysqli_fetch_assoc($data)):
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($d['nama']) ?></strong></td>
                        <td><span style="color: var(--text-muted);"><?= htmlspecialchars($d['email']) ?></span></td>
                        <td><?= htmlspecialchars($d['judul']) ?></td>
                        <td><strong style="color: var(--danger); font-size: 14px;">Rp <?= number_format($d['jumlah']) ?></strong></td>
                        <td>
                            <?php if($d['status'] == 'belum bayar'): ?>
                                <span class="badge badge-danger">Belum Bayar</span>
                            <?php else: ?>
                                <span class="badge badge-success">Sudah Lunas</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if($d['status'] == 'belum bayar'): ?>
                                <a href="bayar_denda.php?id=<?= $d['id'] ?>&email=<?= urlencode($email) ?>" 
                                   class="btn btn-success btn-sm"
                                   data-jumlah="<?= number_format($d['jumlah']); ?>"
                                   onclick="return bukaKonfirmasi(this.href, 'Konfirmasi pelunasan denda sebesar Rp ' + this.getAttribute('data-jumlah') + '?', 'Pelunasan Denda', 'success', 'fa-solid fa-money-bill')">
                                   <i class="fa-solid fa-money-bill"></i> Lunaskan
                                </a>
                            <?php else: ?>
                                <span style="color: var(--text-dim); font-size: 12.5px;">- Selesai -</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>
