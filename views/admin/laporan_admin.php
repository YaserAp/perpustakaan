<?php
require_once __DIR__ . '/../../config/koneksi.php';

$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');
?>

<div class="page-header">
    <div class="header-title">
        <h2> Laporan & Rekapitulasi</h2>
        <p>Cetak rekapitulasi data peminjaman dan denda dalam format laporan PDF/Print.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">

    <!-- CARD LAPORAN PEMINJAMAN -->
    <div class="table-card" style="padding: 20px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <h3 style="font-size: 16px; margin: 0; color: var(--text-main);">Laporan Peminjaman Buku</h3>
                <p style="font-size: 12px; color: var(--text-muted); margin: 0;">Rekap riwayat transaksi peminjaman berdasarkan tanggal.</p>
            </div>
        </div>

        <form action="cetak_laporan.php" method="GET" target="_blank">
            <input type="hidden" name="jenis" value="peminjaman">
            
            <div style="margin-bottom: 12px;">
                <label class="form-label"><i class="fa-solid fa-calendar"></i> Tanggal Mulai:</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= htmlspecialchars($tgl_awal) ?>" required>
            </div>

            <div style="margin-bottom: 18px;">
                <label class="form-label"><i class="fa-solid fa-calendar-check"></i> Tanggal Sampai:</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= htmlspecialchars($tgl_akhir) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; height: 40px; justify-content: center;">
                <i class="fa-solid fa-print"></i> Cetak Laporan Peminjaman
            </button>
        </form>
    </div>

    <!-- CARD LAPORAN DENDA -->
    <div class="table-card" style="padding: 20px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--danger-bg); color: var(--danger); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <h3 style="font-size: 16px; margin: 0; color: var(--text-main);">Laporan Rekap Denda</h3>
                <p style="font-size: 12px; color: var(--text-muted); margin: 0;">Rekapitulasi seluruh tagihan denda & status lunas.</p>
            </div>
        </div>

        <form action="cetak_laporan.php" method="GET" target="_blank">
            <input type="hidden" name="jenis" value="denda">
            
            <div style="margin-bottom: 12px;">
                <label class="form-label"><i class="fa-solid fa-calendar"></i> Tanggal Mulai:</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= htmlspecialchars($tgl_awal) ?>" required>
            </div>

            <div style="margin-bottom: 18px;">
                <label class="form-label"><i class="fa-solid fa-calendar-check"></i> Tanggal Sampai:</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= htmlspecialchars($tgl_akhir) ?>" required>
            </div>

            <button type="submit" class="btn btn-danger" style="width: 100%; height: 40px; justify-content: center;">
                <i class="fa-solid fa-print"></i> Cetak Laporan Denda
            </button>
        </form>
    </div>

</div>
