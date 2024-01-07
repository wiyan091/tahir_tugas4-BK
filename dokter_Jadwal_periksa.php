<?php
if (!isset($_SESSION)) {
    session_start();
}

// Include the database connection file (koneksi.php)
include_once("db_koneksi.php");

// ...

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_jadwal_periksa_modal'])) {
    $id = $_POST['id'];
    $newIdDokter = $_POST['new_id_dokter'];
    $newHari = $_POST['new_hari'];
    $newJamMulai = $_POST['new_jam_mulai'];
    $newJamSelesai = $_POST['new_jam_selesai'];


    // Check for overlapping schedules excluding the current schedule being updated
    $checkOverlapQuery = "SELECT id FROM jadwal_periksa WHERE id_dokter=? AND hari=? AND ((jam_mulai <= ? AND jam_selesai >= ?) OR (jam_mulai <= ? AND jam_selesai >= ?)) AND id <> ?";
    $stmtOverlap = $mysqli->prepare($checkOverlapQuery);
    $stmtOverlap->bind_param("isssssi", $newIdDokter, $newHari, $newJamMulai, $newJamMulai, $newJamSelesai, $newJamSelesai, $id);
    $stmtOverlap->execute();
    $stmtOverlap->store_result();

    if ($stmtOverlap->num_rows > 0) {
        // Overlapping schedule exists
        echo "Error: Sudah ada hari yang sama";
        $stmtOverlap->close();
    } else {
        // No overlapping schedule, proceed with update
        $stmtOverlap->close();

        // Update jadwal_periksa in the database using prepared statement
        $updateQuery = "UPDATE jadwal_periksa SET id_dokter=?, hari=?, jam_mulai=?, jam_selesai=? WHERE id=?";
        $stmt = $mysqli->prepare($updateQuery);
        $stmt->bind_param("isssi", $newIdDokter, $newHari, $newJamMulai, $newJamSelesai, $id);

        if ($stmt->execute()) {
            // Update successful
            header("Location: dokter_Menu_jadwal_periksa.php");
            exit();
        } else {
            // Update failed, handle error (you may redirect or display an error message)
            echo "Update failed: " . $stmt->error;
        }

        $stmt->close();
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_jadwal_periksa'])) {
    $newIdDokter = $_POST['add_id_dokter'];
    $newHari = $_POST['add_hari'];
    $newJamMulai = $_POST['add_jam_mulai'];
    $newJamSelesai = $_POST['add_jam_selesai'];
    $newJamSelesai = date("H:i:s", strtotime($newJamSelesai));

    // Check for overlapping schedules
    $checkOverlapQuery = "SELECT id FROM jadwal_periksa WHERE id_dokter=? AND hari=? AND ((jam_mulai <= ? AND jam_selesai >= ?) OR (jam_mulai <= ? AND jam_selesai >= ?))";
    $stmtOverlap = $mysqli->prepare($checkOverlapQuery);
    $stmtOverlap->bind_param("isssss", $newIdDokter, $newHari, $newJamMulai, $newJamMulai, $newJamSelesai, $newJamSelesai);
    $stmtOverlap->execute();
    $stmtOverlap->store_result();

    if ($stmtOverlap->num_rows > 0) {
        // Overlapping schedule exists
        echo "Error: Jadwal dokter yang tumpang pilih hari lain.";
        $stmtOverlap->close();
    } else {
        // No overlapping schedule, proceed with insertion
        $stmtOverlap->close();

        // Insert new jadwal_periksa into the database using prepared statement
        $insertQuery = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insertQuery);
        $stmt->bind_param("isss", $newIdDokter, $newHari, $newJamMulai, $newJamSelesai);  // Corrected binding parameters

        if ($stmt->execute()) {
            // Insertion successful
            header("Location: dokter_Menu_jadwal_periksa.php");
            exit();
        } else {
            // Insertion failed, handle error (you may redirect or display an error message)
            echo "Insertion failed: " . $stmt->error;
        }

        $stmt->close();
    }
}



// Fetch data from the 'jadwal_periksa' table with doctor names
$jadwalPeriksaQuery = "SELECT j.*, d.nama 
                        FROM jadwal_periksa j
                        JOIN dokter d ON j.id_dokter = d.id";
$jadwalPeriksaResult = $mysqli->query($jadwalPeriksaQuery);

// Fetch the data as an associative array
$jadwalPeriksaData = $jadwalPeriksaResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Add your head section here -->

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css">

    <!-- Add other necessary CSS links here -->
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addModal">Tambah Jadwal Periksa</button>
                                <br><br>
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ID Dokter</th>
                                            <th>Hari</th>
                                            <th>Jam Mulai</th>
                                            <th>Jam Selesai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $counter = 1; // Variabel penanda nomor urut
                                        foreach ($jadwalPeriksaData as $jadwalPeriksaRow) {
                                            echo "<tr>";
                                            echo "<td>" . $counter . "</td>"; // Gunakan counter sebagai nomor urut
                                            $counter++; // Tingkatkan counter setiap kali loop
                                            echo "<td>" . $jadwalPeriksaRow['nama'] . "</td>"; // Display doctor's name
                                            echo "<td>" . $jadwalPeriksaRow['hari'] . "</td>";
                                            echo "<td>" . $jadwalPeriksaRow['jam_mulai'] . "</td>";
                                            echo "<td>" . $jadwalPeriksaRow['jam_selesai'] . "</td>";
                                            echo "<td>
                <form method='post' action=''>
                    <input type='hidden' name='id' value='" . $jadwalPeriksaRow['id'] . "'>
                    <input type='hidden' name='new_id_dokter' value='" . $jadwalPeriksaRow['id_dokter'] . "'>
                    <input type='hidden' name='new_hari' value='" . $jadwalPeriksaRow['hari'] . "'>
                    <input type='hidden' name='new_jam_mulai' value='" . $jadwalPeriksaRow['jam_mulai'] . "'>
                    <input type='hidden' name='new_jam_selesai' value='" . $jadwalPeriksaRow['jam_selesai'] . "'>

                    <button type='button' name='update_jadwal_periksa' class='btn btn-warning btn-sm update-btn' data-toggle='modal' data-target='#updateModal' 
                    data-id='" . $jadwalPeriksaRow['id'] . "' 
                    data-id_dokter='" . $jadwalPeriksaRow['id_dokter'] . "' 
                    data-hari='" . $jadwalPeriksaRow['hari'] . "' 
                    data-jam_mulai='" . $jadwalPeriksaRow['jam_mulai'] . "'
                    data-jam_selesai='" . $jadwalPeriksaRow['jam_selesai'] . "'>Update</button>
                    
                    <!-- Delete form moved outside the update form -->
                    

            </td>";
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

    <!-- Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Perbarui Jadwal Periksa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="dokter_Menu_jadwal_periksa.php">
                        <!-- Replace with the actual update PHP file -->
                        <input type="hidden" name="id" id="update_id">
                        <!-- Inside the update modal form -->
                        <div class="form-group" style="display: none;">
                            <label for="update_id_dokter">ID Dokter</label>
                            <input type="hidden" class="form-control" id="update_id_dokter" name="new_id_dokter" required>
                        </div>
                        <div class="form-group">
                            <label for="update_hari">Hari</label>
                            <select class="form-control" id="update_hari" name="new_hari" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="update_jam_mulai">Jam Mulai</label>
                            <input type="text" class="form-control" id="update_jam_mulai" name="new_jam_mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="update_jam_selesai">Jam Selesai</label>
                            <input type="text" class="form-control" id="update_jam_selesai" name="new_jam_selesai" required>
                        </div>
                        <button type="submit" name="update_jadwal_periksa_modal" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding jadwal_periksa -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Jadwal Periksa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="dokter_Menu_jadwal_periksa.php">
                        <!-- Replace with the actual add PHP file -->
                        <div class="form-group">
                            <select class="form-control" id="add_id_dokter" name="add_id_dokter" required>
                                <?php
                                // Fetch the list of doctors from your database
                                $fetchDoctorsQuery = "SELECT id, nama FROM dokter";
                                $resultDoctors = $mysqli->query($fetchDoctorsQuery);

                                while ($doctor = $resultDoctors->fetch_assoc()) {
                                    echo "<option value='{$doctor["id"]}'>{$doctor["nama"]} (ID: {$doctor["id"]})</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="add_hari">Hari</label>
                                <select class="form-control" id="add_hari" name="add_hari" required>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="add_jam_mulai">Jam Mulai</label>
                            <input type="time" class="form-control" id="add_jam_mulai" name="add_jam_mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="add_jam_selesai">Jam Selesai</label>
                            <input type="time" class="form-control" id="add_jam_selesai" name="add_jam_selesai" required>
                        </div>
                        <button type="submit" name="add_jadwal_periksa" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>

    <!-- Add other necessary script includes here -->

    <!-- ... Your HTML code ... -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add your JavaScript code here
            var updateButtons = document.querySelectorAll('.update-btn');
            var deleteButtons = document.querySelectorAll('.btn-danger');

            updateButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var id = button.getAttribute('data-id');
                    var id_dokter = button.getAttribute('data-id_dokter');
                    var hari = button.getAttribute('data-hari');
                    var jam_mulai = button.getAttribute('data-jam_mulai');
                    var jam_selesai = button.getAttribute('data-jam_selesai');

                    document.getElementById('update_id').value = id;
                    document.getElementById('update_id_dokter').value = id_dokter;
                    document.getElementById('update_hari').value = hari;
                    document.getElementById('update_jam_mulai').value = jam_mulai;
                    document.getElementById('update_jam_selesai').value = jam_selesai;
                });
            });

        });
    </script>

</body>

</html>