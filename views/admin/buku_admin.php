<?php
require_once __DIR__ . '/../../config/koneksi.php';

$data = mysqli_query($conn, "SELECT * FROM buku ORDER BY id DESC");
?>

<div class="page-header">
    <div class="header-title">
        <h2> Kelola Data Buku</h2>
        <p>Tambah, ubah, atau hapus koleksi buku perpustakaan.</p>
    </div>
    <a href="tambah_buku.php" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Buku Baru
    </a>
</div>

<div class="table-card">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div class="search-box" style="max-width: 320px;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="tableFilterInput" class="form-control" placeholder="Cari judul atau penulis buku...">
        </div>
        <span style="font-size: 13px; color: var(--text-muted);">Total Buku: <strong><?= mysqli_num_rows($data) ?></strong></span>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 70px;">Cover</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th style="width: 120px;">Stok</th>
                    <th style="width: 120px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1; 
                if(mysqli_num_rows($data) == 0):
                ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">
                            Belum ada data buku tersedia.
                        </td>
                    </tr>
                <?php 
                else:
                    while($b = mysqli_fetch_assoc($data)): 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <img src="assets/img/<?= htmlspecialchars($b['gambar'] ?: 'default.jpg') ?>" 
                                 style="width: 44px; height: 58px; object-fit: cover; border-radius: 4px;"
                                 onerror="this.src='assets/img/default.jpg'">
                        </td>
                        <td>
                            <strong style="color: var(--text-main); font-size: 14px;"><?= htmlspecialchars($b['judul']) ?></strong>
                        </td>
                        <td><span style="color: var(--text-muted);"><?= htmlspecialchars($b['penulis']) ?></span></td>
                        <td>
                            <?php if($b['stok'] > 0): ?>
                                <span class="badge badge-success">Tersedia (<?= $b['stok'] ?>)</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Habis (0)</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <button type="button" 
                                        class="btn btn-secondary btn-sm" 
                                        title="Lihat & Cetak QR Code"
                                        onclick="lihatQRCode('<?= $b['id'] ?>', '<?= htmlspecialchars(addslashes($b['judul'])) ?>', '<?= htmlspecialchars(addslashes($b['penulis'])) ?>')">
                                    <i class="fa-solid fa-qrcode"></i>
                                </button>
                                <a href="edit_buku.php?id=<?= $b['id'] ?>" class="btn btn-secondary btn-sm" title="Edit Buku">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="hapus_buku.php?id=<?= $b['id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   title="Hapus Buku"
                                   data-judul="<?= htmlspecialchars($b['judul'], ENT_QUOTES); ?>"
                                   onclick="return bukaKonfirmasi(this.href, 'Apakah Anda yakin ingin menghapus buku ' + this.getAttribute('data-judul') + '?', 'Hapus Buku', 'danger')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL LIHAT & CETAK QR CODE BUKU -->
<div id="modalQRCode" class="modal-overlay">
    <div class="modal-content" style="max-width: 380px; text-align: center;">
        <div class="modal-header">
            <h3><i class="fa-solid fa-qrcode"></i> QR Code Buku</h3>
            <button class="modal-close" onclick="closeModal('modalQRCode')">&times;</button>
        </div>

        <div style="padding: 20px 10px;">
            <h4 id="qrModalJudul" style="font-size: 15px; margin-bottom: 4px; color: var(--text-main);"></h4>
            <p id="qrModalPenulis" style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;"></p>
            
            <div id="qrCodeContainer" style="display: flex; justify-content: center; align-items: center; background: #ffffff; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); width: 200px; height: 200px; margin: 0 auto 16px auto; box-shadow: var(--shadow-sm);"></div>
            
            <p style="font-size: 11.5px; color: var(--text-muted); margin-bottom: 20px;">
                Tempelkan QR Code ini pada sampul fisik buku untuk dipindai saat peminjaman.
            </p>

            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="closeModal('modalQRCode')">Tutup</button>
                <button type="button" class="btn btn-primary" style="flex: 1;" onclick="cetakQRCode()">
                    <i class="fa-solid fa-print"></i> Cetak QR
                </button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/qrcode.min.js"></script>
<script>
let currentQRCoder = null;

function lihatQRCode(id, judul, penulis) {
    document.getElementById('qrModalJudul').innerText = judul;
    document.getElementById('qrModalPenulis').innerText = "Penulis: " + penulis;
    
    const container = document.getElementById('qrCodeContainer');
    container.innerHTML = "";
    
    const qrData = JSON.stringify({ type: "buku", id: parseInt(id), code: "BUKU-" + id });
    
    new QRCode(container, {
        text: qrData,
        width: 168,
        height: 168,
        colorDark : "#0f172a",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
    
    openModal('modalQRCode');
}

function cetakQRCode() {
    const judul = document.getElementById('qrModalJudul').innerText;
    const penulis = document.getElementById('qrModalPenulis').innerText;
    const containerHTML = document.getElementById('qrCodeContainer').innerHTML;

    const win = window.open('', '', 'width=450,height=500');
    win.document.write(`
        <html>
        <head>
            <title>Cetak QR Code - ${judul}</title>
            <style>
                body { font-family: sans-serif; text-align: center; padding: 40px 20px; }
                .card { border: 2px dashed #333; padding: 24px; border-radius: 12px; display: inline-block; }
                h3 { margin: 0 0 6px 0; font-size: 18px; }
                p { margin: 0 0 16px 0; font-size: 13px; color: #555; }
                .qr { display: flex; justify-content: center; margin-bottom: 12px; }
                .tag { font-size: 11px; background: #eee; padding: 4px 8px; border-radius: 4px; display: inline-block; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="card">
                <h3>${judul}</h3>
                <p>${penulis}</p>
                <div class="qr">${containerHTML}</div>
                <div class="tag">PERPUSTAKAAN DIGITAL RPL 2</div>
            </div>
            <script>
                window.onload = function() { window.print(); window.close(); }
            <\/script>
        </body>
        </html>
    `);
    win.document.close();
}
</script>

