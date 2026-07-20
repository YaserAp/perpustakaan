<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

ob_clean();

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID buku tidak valid.']);
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM buku WHERE id = '$id'");
$buku = mysqli_fetch_assoc($query);

if ($buku) {
    // Fetch active borrowers for Admin Quick Return
    $peminjam_query = mysqli_query($conn, "
        SELECT peminjaman.id as peminjaman_id, users.nama, users.email, peminjaman.tanggal_pinjam, peminjaman.tanggal_kembali
        FROM peminjaman
        JOIN users ON users.id = peminjaman.user_id
        WHERE peminjaman.buku_id = '$id' AND peminjaman.status = 'dipinjam'
        ORDER BY peminjaman.id DESC
    ");

    $peminjam_list = [];
    if ($peminjam_query) {
        while($p = mysqli_fetch_assoc($peminjam_query)) {
            $peminjam_list[] = [
                'peminjaman_id' => (int)$p['peminjaman_id'],
                'nama' => $p['nama'],
                'email' => $p['email'],
                'tanggal_pinjam' => date('d M Y', strtotime($p['tanggal_pinjam'])),
                'tanggal_kembali' => date('d M Y', strtotime($p['tanggal_kembali']))
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'buku' => [
            'id' => (int)$buku['id'],
            'judul' => $buku['judul'],
            'penulis' => $buku['penulis'],
            'stok' => (int)$buku['stok'],
            'gambar' => $buku['gambar'] ?: 'default.jpg',
            'peminjam' => $peminjam_list
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Buku tidak ditemukan dalam database.']);
}
exit;
