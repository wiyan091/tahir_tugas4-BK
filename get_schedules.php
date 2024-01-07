<?php

// Include your database connection file
include 'db_koneksi.php';
// Periksa koneksi
if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

// Ambil id_poli dari data yang diterima
$poli_id = $_POST['poli_id'];

// Kueri SQL untuk mengambil jadwal dokter berdasarkan id_poli
$queryJadwal = "SELECT d.id AS dokter_id, d.nama AS nama_dokter, j.id AS jadwal_id, j.hari, j.jam_mulai, j.jam_selesai
                FROM dokter d
                JOIN jadwal_periksa j ON j.id_dokter = d.id
                WHERE d.id_poli = $poli_id";

$resultJadwal = $mysqli->query($queryJadwal);

// Buat HTML options untuk opsi jadwal
$options = "";
if ($resultJadwal && $resultJadwal->num_rows > 0) {
    while ($rowJadwal = $resultJadwal->fetch_assoc()) {
        $options .= '<option value="' . $rowJadwal['jadwal_id'] . '">' . $rowJadwal['nama_dokter'] . ' | ' . $rowJadwal['hari'] . ' (' . $rowJadwal['jam_mulai'] . ' - ' . $rowJadwal['jam_selesai'] . ')' . '</option>';
    }
}

// Tutup koneksi database
$mysqli->close();

// Mengembalikan opsi jadwal dalam format HTML
echo $options;
