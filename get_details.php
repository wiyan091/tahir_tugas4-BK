<?php

// Include your database connection file
include 'db_koneksi.php';

// Aktifkan laporan kesalahan
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['pasien_id']) && is_numeric($_POST['pasien_id'])) {
    $pasienId = (int)$_POST['pasien_id'];

    // Gunakan prepared statement untuk mencegah SQL injection
    $detailsQuery = "SELECT p.nama AS nama_pasien, tp.tgl_periksa, tp.catatan, tp.biaya_periksa, d.nama AS nama_dokter, jw.id_dokter, dp.keluhan, o.nama_obat, dpk.id_periksa
                FROM pasien p
                JOIN daftar_poli dp ON p.id = dp.id_pasien
                JOIN periksa tp ON dp.id = tp.id_daftar_poli
                JOIN jadwal_periksa jw ON dp.id_jadwal = jw.id
                JOIN dokter d ON jw.id_dokter = d.id
                JOIN detail_periksa dpk ON tp.id = dpk.id_periksa
                JOIN obat o ON dpk.id_obat = o.id
                WHERE p.id = ?";

    // Selanjutnya, siapkan statement
    $stmt = $mysqli->prepare($detailsQuery);

    // Bind parameter
    $stmt->bind_param("i", $pasienId);

    // Eksekusi statement
    $stmt->execute();

    // Ambil hasil query
    $result = $stmt->get_result();

    // Ambil data
    $resultArray = array();

    while ($row = $result->fetch_assoc()) {
        $idPeriksa = $row['id_periksa'];

        // Jika id_periksa tidak ada dalam array hasil, inisialisasi array untuknya
        if (!isset($resultArray[$idPeriksa])) {
            $resultArray[$idPeriksa] = $row;
            $resultArray[$idPeriksa]['nama_obat'] = array($row['nama_obat']);
        } else {
            // Jika id_periksa sudah ada dalam array hasil, gabungkan nilai nama_obat
            $resultArray[$idPeriksa]['nama_obat'][] = $row['nama_obat'];
        }
    }

    // Tutup statement
    $stmt->close();

    // Tampilkan detail dalam tabel
    echo '<table class="table table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th> Nama Pasien </th>';
    echo '<th> Tanggal Periksa </th>';
    echo '<th> Catatan </th>';
    echo '<th> Biaya Periksa </th>';
    echo '<th>Nama Dokter</th>';
    echo '<th>Keluhan</th>';
    echo '<th> Nama Obat </th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($resultArray as $resultRow) {
        echo '<tr>';
        echo '<td>' . $resultRow['nama_pasien'] . '</td>';
        echo '<td>' . $resultRow['tgl_periksa'] . '</td>';
        echo '<td>' . $resultRow['catatan'] . '</td>';
        echo '<td>Rp ' . number_format($resultRow['biaya_periksa'], 0, ',', '.') . '</td>';
        echo '<td>' . $resultRow['nama_dokter'] . '</td>';
        echo '<td>' . $resultRow['keluhan'] . '</td>';
        echo '<td>' . implode(", ", $resultRow['nama_obat']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo 'Invalid request.';
}
