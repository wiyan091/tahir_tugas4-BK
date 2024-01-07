<?php
// Start or resume the session
if (!isset($_SESSION)) {
    session_start();
}

// Include the database connection file
include_once("db_koneksi.php");

/// Query to fetch data from pasien and daftar_poli tables with status_periksa
$pasienQuery = "SELECT pasien.id, pasien.nama, daftar_poli.id AS id_daftar_poli, daftar_poli.keluhan, daftar_poli.status_periksa
                FROM pasien 
                INNER JOIN daftar_poli ON pasien.id = daftar_poli.id_pasien";

// Prepare and execute the query
$stmt = $mysqli->prepare($pasienQuery);

if ($stmt === false) {
    die("Error in preparing statement");
}

$stmt->execute();

// Get the result and fetch data
$pasienResult = $stmt->get_result();
$pasienData = $pasienResult->fetch_all(MYSQLI_ASSOC);

$stmt->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['periksa_id_pasien'], $_POST['periksa_status'])) {
    // Check if the specific POST variables are set
    if (isset($_POST['periksa_id_pasien'], $_POST['periksa_status'])) {
        // Get data from the form
        $id_pasien = $_POST['periksa_id_pasien'];
        $status_periksa = $_POST['periksa_status'];

        // Update the daftar_poli table
        $updateQuery = "UPDATE daftar_poli SET status_periksa = ? WHERE id_pasien = ?";
        $updateStmt = $mysqli->prepare($updateQuery);

        if ($updateStmt === false) {
            die("Error in preparing update statement: " . $mysqli->error);
        }

        // Tipe data "s" untuk string, "i" untuk integer
        $updateStmt->bind_param("si", $status_periksa, $id_pasien);

        if ($updateStmt->execute() === false) {
            die("Error in executing update statement: " . $updateStmt->error);
        } else {
            // Logging: Tulis ke file log atau outputkan ke console
            file_put_contents('update_log.txt', "Update successful for id_pasien: $id_pasien\n", FILE_APPEND);
        }

        $updateStmt->close();

        // Redirect back to the original page after successful submission
        header("Location: dokter_Menu_periksa.php");
        exit();
    } else {
        // Handle the case where expected POST variables are not set
        die("Error: Missing POST variables");
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_simpan'])) {
    // Check if the specific POST variables are set
    if (isset($_POST['id_daftar_poli'], $_POST['tgl_periksa'], $_POST['catatan'], $_POST['edit_obat'])) {
        // Get data from the form
        $id_daftar_poli = $_POST['id_daftar_poli'];
        $tanggal_periksa = $_POST['tgl_periksa'];
        $catatan = $_POST['catatan'];

// Hitung total biaya_periksa dengan menjumlahkan harga obat yang dipilih
        $selected_obat_prices = $_POST['edit_obat'];
        $total_biaya_obat = 0;

        foreach ($selected_obat_prices as $obat_price) {
            $obat_components = explode('|', $obat_price);
            $harga_obat = floatval($obat_components[3]); // Mengasumsikan harga berada di posisi keempat dari array yang dipecah
            $total_biaya_obat += $harga_obat;
        }

        $biaya_periksa = isset($_POST['biaya_periksa']) ? $_POST['biaya_periksa'] : 150000;
        $biaya_periksa += $total_biaya_obat;

// Get the selected obat prices from the multiple select
        $selected_obat_prices = $_POST['edit_obat'];

        // Insert data into the periksa table
        $insertQuery = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) VALUES (?, ?, ?, ?)";
        $insertStmt = $mysqli->prepare($insertQuery);

        if ($insertStmt === false) {
            die("Error dalam menyiapkan pernyataan insert: " . $mysqli->error);
        }

        // Bind parameters and execute the statement
        $insertStmt->bind_param("isss", $id_daftar_poli, $tanggal_periksa, $catatan, $biaya_periksa);
        $insertStmt->execute();
        $insertStmt->close();

        // Get the last inserted id_periksa
        $id_periksa = $mysqli->insert_id;

        // Insert data into detail_periksa table
        $detailInsertQuery = "INSERT INTO detail_periksa (id_periksa, id_obat) VALUES (?, ?)";
        $detailInsertStmt = $mysqli->prepare($detailInsertQuery);

        if ($detailInsertStmt === false) {
            die("Error dalam menyiapkan pernyataan insert detail_periksa: " . $mysqli->error);
        }

        // Loop through selected obat and insert into detail_periksa
        foreach ($selected_obat_prices as $obat_price) {
            $obat_components = explode('|', $obat_price);
            $id_obat = $obat_components[0];

            $detailInsertStmt->bind_param("ii", $id_periksa, $id_obat);
            $detailInsertStmt->execute();
        }

        $detailInsertStmt->close();

        // Redirect back to the original page after successful submission
        header("Location: dokter_Menu_periksa.php");
        exit();
    } else {
        // Handle the case where expected POST variables are not set
        die("Error: Missing POST variables (add_simpan)");
    }
}


?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Data Pasien</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css">

    <style>
        .aksi-btn {
            margin-right: 5px;
        }

        table {
            width: 100%;
        }
    </style>
    <style>
        .hidden-form {
            display: none;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>Keluhan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $counter = 1; // Variabel penanda nomor urut
                                        foreach ($pasienData as $pasienRow) {
                                            echo "<tr>";
                                            echo "<td>" . $counter . "</td>"; // Gunakan counter sebagai nomor urut
                                            $counter++; // Tingkatkan counter setiap kali loop
                                            echo "<td>" . $pasienRow['nama'] . "</td>";
                                            echo "<td>" . $pasienRow['keluhan'] . "</td>";
                                            echo "<td>";

                                            // Check if the 'status_periksa' key exists in the $pasienRow array
                                            if (array_key_exists('status_periksa', $pasienRow)) {
                                                // Check the status and display the appropriate button
                                                if ($pasienRow['status_periksa'] == 1) {
                                                    // Status is 1, hide "Periksa" button and show "Edit" button
                                                    echo "<button class='btn btn-primary aksi-btn' data-toggle='modal' data-target='#editModal' data-id='" . $pasienRow['id'] . "'>Edit</button>";
                                                } else {
                                                    // Status is 0, hide "Edit" button and show "Periksa" button
                                                    echo "<button class='btn btn-success aksi-btn' data-toggle='modal' data-target='#periksaModal' data-id='" . $pasienRow['id'] . "'>Periksa</button>";
                                                }
                                            } else {
                                                // Handle the case where 'status_periksa' key is not present in the array
                                                echo "Status not available";
                                            }

                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Periksa Modal -->
    <div class="modal fade" id="periksaModal" tabindex="-1" role="dialog" aria-labelledby="periksaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="periksaModalLabel">Update Status Periksa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="dokter_Menu_periksa.php">
                        <input type="hidden" name="periksa_id_pasien" id="periksa_id_pasien" readonly>
                        <div class="form-group">
                            <label for="periksa_status">Status Periksa</label>
                            <select class="form-control" id="periksa_status" name="periksa_status">
                                <option value="1">Sudah Diperiksa</option>
                                <option value="0">Belum Diperiksa</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Pasien</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="dokter_Menu_periksa.php">
                        <input type="hidden" name="id_pasien" id="edit_id_pasien">
                        <!-- <input type="" name="id" id="edit_daftar_poli"> -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_nama">Nama</label>
                                    <input type="text" class="form-control" id="edit_nama" name="edit_nama" value="<?php echo $pasienData[0]['nama']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tgl_periksa">Tanggal Periksa</label>
                                    <!-- Menggunakan elemen input tipe datetime-local -->
                                    <input type="datetime-local" class="form-control" id="tgl_periksa" name="tgl_periksa" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan"></textarea>
                        </div>

                        <div class="form-group hidden-form">
                            <label for="id_daftar_poli">daftar_poli</label>
                            <input type="hideen" name="id_daftar_poli" id="edit_daftar_poli">
                        </div>
                        <div class="form-group">
                            <label for="edit_obat">Obat</label>
                            <select class="form-control" id="edit_obat" name="edit_obat[]" multiple>
                                <?php
                                $queryobat = "SELECT * FROM obat";
                                $resultobat = $mysqli->query($queryobat);

                                while ($detailRow = $resultobat->fetch_assoc()) {
                                    if ($detailRow['id_obat'] == $id_obat && $detailRow['harga'] > 0) {
                                        // Use a more readable separator in the value attribute
                                        $optionValue = $detailRow['id']  . '|' . $detailRow['nama_obat'] . '|' . $detailRow['kemasan'] . '|' . $detailRow['harga'];

                                        // Format the "harga" as IDR using number_format
                                        $formattedHarga = 'IDR ' . number_format($detailRow['harga'], 0, ',', '.');

                                        // Use htmlspecialchars for the displayed option text
                                        $optionText = htmlspecialchars($detailRow['id'] . ' - ' . $detailRow['nama_obat'] . ' - ' . $detailRow['kemasan'] . ' - ' . $formattedHarga);

                                        // Set both the value and text attributes of the <option> element
                                        echo "<option value='" . $optionValue . "'>" . $optionText . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <button type="submit" name="add_simpan" class="btn btn-primary btn-block">Simpan</button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- JavaScript untuk menangani data pada modal -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Tangkap klik pada tombol "Periksa"
            $('.btn-success').click(function() {
                // Ambil nilai ID pasien dari atribut data-id tombol
                var id_pasien = $(this).data('id');

                // Set nilai ID pasien ke elemen input periksa_id_pasien pada modal
                $('#periksa_id_pasien').val(id_pasien);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Tangkap klik pada tombol "Edit"
            $('.btn-primary').click(function() {
                // Ambil nilai ID pasien dari atribut data-id tombol
                var id_pasien = $(this).data('id');

                // Temukan data pasien yang sesuai dengan ID pasien yang dipilih
                var selectedPasien = <?php echo json_encode($pasienData); ?>;
                var pasienData = selectedPasien.find(pasien => pasien.id == id_pasien);

                // Set nilai ID pasien ke elemen input edit_id_pasien pada modal
                $('#edit_id_pasien').val(id_pasien);

                // Set nilai id_daftar_poli pada elemen input id_daftar_poli pada modal
                $('#edit_daftar_poli').val(pasienData.id_daftar_poli);

                // Set nilai Nama pada elemen input edit_nama pada modal
                $('#edit_nama').val(pasienData.nama);
            });
        });
    </script>


</body>

</html>