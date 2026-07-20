<?php
require_once __DIR__ . '/../../config/koneksi.php';

$data = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<div class="page-header">
    <div class="header-title">
        <h2> Kelola Data User</h2>
        <p>Daftar semua pengguna dan administrator sistem.</p>
    </div>
</div>

<div class="table-card">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div class="search-box" style="max-width: 320px;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="tableFilterInput" class="form-control" placeholder="Cari nama atau email user...">
        </div>
        <span style="font-size: 13px; color: var(--text-muted);">Total User: <strong><?= mysqli_num_rows($data) ?></strong></span>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th style="width: 120px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                while($u = mysqli_fetch_assoc($data)): 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="avatar" style="width: 28px; height: 28px; font-size: 12px;">
                                    <?= strtoupper(substr($u['nama'], 0, 1)) ?>
                                </div>
                                <strong><?= htmlspecialchars($u['nama']) ?></strong>
                            </div>
                        </td>
                        <td><span style="color: var(--text-muted);"><?= htmlspecialchars($u['email']) ?></span></td>
                        <td>
                            <?php if(($u['role'] ?? 'user') == 'admin'): ?>
                                <span class="badge badge-info"><i class="fa-solid fa-user-shield"></i> Admin</span>
                            <?php else: ?>
                                <span class="badge badge-success"><i class="fa-solid fa-user"></i> User</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-secondary btn-sm" title="Edit User">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="hapus_user.php?id=<?= $u['id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   title="Hapus User"
                                   data-nama="<?= htmlspecialchars($u['nama'], ENT_QUOTES); ?>"
                                   onclick="return bukaKonfirmasi(this.href, 'Apakah Anda yakin ingin menghapus user ' + this.getAttribute('data-nama') + '?', 'Hapus User', 'danger')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
