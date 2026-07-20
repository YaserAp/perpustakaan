<?php
include 'koneksi.php';

$email = $_GET['email'] ?? '';

if(!empty($email)){

    $email = mysqli_real_escape_string($conn, $email);

    $data = mysqli_query($conn, "
    SELECT 
        users.nama, 
        users.email, 
        buku.judul, 
        peminjaman.id,
        peminjaman.tanggal_pinjam,
        peminjaman.tanggal_kembali,
        DATEDIFF(NOW(), peminjaman.tanggal_kembali) AS telat
    FROM peminjaman
    JOIN users ON users.id = peminjaman.user_id
    JOIN buku ON buku.id = peminjaman.buku_id
    WHERE users.email='$email' AND peminjaman.status='dipinjam'
    ");

    while($d = mysqli_fetch_assoc($data)){

        if($d['telat'] > 0){

            $denda = $d['telat'] * 300;
            $id_peminjaman = $d['id'];

            // CEK BIAR GA DOUBLE
            $cek = mysqli_query($conn, "
            SELECT * FROM denda WHERE peminjaman_id='$id_peminjaman'
            ");

            if(mysqli_num_rows($cek) == 0){
                mysqli_query($conn, "
                INSERT INTO denda (peminjaman_id, jumlah, status)
                VALUES ('$id_peminjaman', '$denda', 'belum bayar')
                ");
            }
        }
    }
}
?>