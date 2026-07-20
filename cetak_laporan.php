<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak. Halaman ini khusus Admin.");
}

$jenis = $_GET['jenis'] ?? 'peminjaman';
$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perpustakaan - <?= ucfirst($jenis) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #fff; color: #0f172a; font-family: Arial, sans-serif; padding: 25px; line-height: 1.5; }
        .kop-header { text-align: center; border-bottom: 3px double #0f172a; padding-bottom: 12px; margin-bottom: 20px; }
        .kop-header h2 { margin: 0; font-size: 22px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; }
        .kop-header p { margin: 4px 0 0 0; font-size: 13px; color: #475569; }
        
        .report-title { text-align: center; margin-bottom: 20px; }
        .report-title h3 { margin: 0; font-size: 16px; text-transform: uppercase; text-decoration: underline; }
        .report-title p { margin: 4px 0 0 0; font-size: 12.5px; color: #64748b; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 12.5px; }
        table th, table td { border: 1px solid #334155; padding: 8px 10px; text-align: left; }
        table th { background: #e2e8f0; font-weight: 600; text-transform: uppercase; font-size: 11.5px; }

        .badge-print { padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .ttd-container { margin-top: 40px; display: flex; justify-content: flex-end; }
        .ttd-box { text-align: center; width: 220px; font-size: 13px; }

        .btn-print { background: #2563eb; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; }
        .btn-close { background: #64748b; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 12px 18px; border-radius: 8px; border: 1px solid #cbd5e1;">
        <div>
            <i class="fa-solid fa-file-pdf" style="color: #ef4444; margin-right: 6px;"></i>
            <strong>Pratinjau Cetak Laporan <?= ucfirst($jenis) ?></strong> 
            <span style="color: #64748b; font-size: 13px; margin-left: 8px;">(Periode: <?= date('d M Y', strtotime($tgl_awal)) ?> s.d. <?= date('d M Y', strtotime($tgl_akhir)) ?>)</span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-print"><i class="fa-solid fa-print"></i> Cetak / Simpan PDF</button>
            <button onclick="window.close()" class="btn-close">Tutup</button>
        </div>
    </div>

    <div class="kop-header">
        <h2>PERPUSTAKAAN DIGITAL</h2>
        <p>Jl. Pendidikan No. 10, Jakarta | Email: perpustakaan@.sch.id | Telp: (021) 555-0199</p>
    </div>

    <div class="report-title">
        <h3>LAPORAN REKAPITULASI <?= strtoupper($jenis) ?></h3>
        <p>Periode: <?= date('d F Y', strtotime($tgl_awal)) ?> s/d <?= date('d F Y', strtotime($tgl_akhir)) ?></p>
    </div>

    <?php if ($jenis == 'peminjaman'): ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Nama Anggota</th>
                    <th>Judul Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Batas Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "
                SELECT users.nama, buku.judul, peminjaman.tanggal_pinjam, peminjaman.tanggal_kembali, peminjaman.status
                FROM peminjaman
                JOIN users ON users.id = peminjaman.user_id
                JOIN buku ON buku.id = peminjaman.buku_id
                WHERE peminjaman.tanggal_pinjam BETWEEN '$tgl_awal' AND '$tgl_akhir'
                ORDER BY peminjaman.id DESC
                ";
                $res = mysqli_query($conn, $sql);
                if (mysqli_num_rows($res) == 0):
                ?>
                    <tr><td colspan="6" style="text-align: center; color: #64748b;">Tidak ada data peminjaman pada periode ini.</td></tr>
                <?php
                else:
                    $no = 1;
                    while($row = mysqli_fetch_assoc($res)):
                ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_kembali'])) ?></td>
                        <td>
                            <?php if($row['status'] == 'dipinjam'): ?>
                                <span class="badge-print badge-warning">Dipinjam</span>
                            <?php else: ?>
                                <span class="badge-print badge-success">Dikembalikan</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>

    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Nama Anggota</th>
                    <th>Email</th>
                    <th>Judul Buku</th>
                    <th>Jumlah Denda</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "
                SELECT users.nama, users.email, buku.judul, denda.jumlah, denda.status
                FROM denda
                JOIN peminjaman ON peminjaman.id = denda.peminjaman_id
                JOIN users ON users.id = peminjaman.user_id
                JOIN buku ON buku.id = peminjaman.buku_id
                ORDER BY denda.id DESC
                ";
                $res = mysqli_query($conn, $sql);
                if (mysqli_num_rows($res) == 0):
                ?>
                    <tr><td colspan="6" style="text-align: center; color: #64748b;">Tidak ada data denda tercatat.</td></tr>
                <?php
                else:
                    $no = 1;
                    $total_denda = 0;
                    while($row = mysqli_fetch_assoc($res)):
                        $total_denda += $row['jumlah'];
                ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><strong>Rp <?= number_format($row['jumlah']) ?></strong></td>
                        <td>
                            <?php if($row['status'] == 'sudah bayar'): ?>
                                <span class="badge-print badge-success">Lunas</span>
                            <?php else: ?>
                                <span class="badge-print badge-danger">Belum Bayar</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                    <tr style="background: #f1f5f9; font-weight: bold;">
                        <td colspan="4" style="text-align: right;">Total Denda Terpencatat:</td>
                        <td colspan="2">Rp <?= number_format($total_denda) ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Jakarta, <?= date('d F Y') ?></p>
            <p style="margin-top: -10px;">Kepala Perpustakaan,</p>
            <div style="height: 55px;"></div>
            <p style="font-weight: bold; text-decoration: underline; margin-bottom: 2px;"><?= htmlspecialchars($_SESSION['nama'] ?? 'Administrator') ?></p>
            <p style="font-size: 11px; color: #64748b; margin-top: 0;">NIP. 19850720 201012 1 002</p>
        </div>
    </div>

</body>
</html>
