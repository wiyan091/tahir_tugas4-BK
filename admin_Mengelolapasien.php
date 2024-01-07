<?php
if (!isset($_SESSION)) {
    session_start();
}

include_once("db_koneksi.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pasien_modal'])) {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $newNamaPasien = isset($_POST['new_nama_pasien']) ? $_POST['new_nama_pasien'] : null;
    $newAlamat = isset($_POST['new_alamat']) ? $_POST['new_alamat'] : null;
    $newNoKTP = isset($_POST['new_no_ktp']) ? $_POST['new_no_ktp'] : null;
    $newNoHP = isset($_POST['new_no_hp']) ? $_POST['new_no_hp'] : null;

    $updateQuery = "UPDATE pasien SET nama=?,  alamat=?, no_ktp=?, no_hp=? WHERE id=?";
    $stmt = $mysqli->prepare($updateQuery);
    $stmt->bind_param("ssssi", $newNamaPasien,  $newAlamat, $newNoKTP, $newNoHP, $id);

    if ($stmt->execute()) {
        header("Location: admin_Menumengelolapasien.php");
        exit();
    } else {
        echo "Update failed: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_pasien'])) {
    $newNamaPasien = isset($_POST['add_nama_pasien']) ? $_POST['add_nama_pasien'] : null;
    $newPassword = isset($_POST['add_password']) ? $_POST['add_password'] : null;
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;
    $newAlamat = isset($_POST['add_alamat']) ? $_POST['add_alamat'] : null;
    $newNoKTP = isset($_POST['add_no_ktp']) ? $_POST['add_no_ktp'] : null;
    $newNoHP = isset($_POST['add_no_hp']) ? $_POST['add_no_hp'] : null;

    if ($newPassword === $confirmPassword) {
        // Membuat tahun dan bulan saat ini (contoh, 202312)
        $tahunBulanSekarang = date('Ym');

        // Mencari nomor rekam medis (no_rm) terakhir dengan prepared statement
        $queryLastNoRM = "SELECT MAX(SUBSTRING(no_rm, 9)) AS max_counter FROM pasien";
        $stmtLastNoRM = $mysqli->prepare($queryLastNoRM);

        if ($stmtLastNoRM === false) {
            die("Error query: " . $mysqli->error);
        }

        $stmtLastNoRM->execute();
        $stmtLastNoRM->bind_result($maxCounter);
        $stmtLastNoRM->fetch();
        $stmtLastNoRM->close();

        $counter = 1; // Nilai default jika tidak ada rekam medis yang ditemukan
        if (!empty($maxCounter)) {
            $counter = (int)$maxCounter + 1;
        }

        // Pad nilai counter dengan leading zeros (contoh, 001, 002, ...)
        $paddedCounter = str_pad($counter, 3, '0', STR_PAD_LEFT);

        // Gabungkan tahunBulanSekarang dan paddedCounter untuk membentuk no_rm akhir
        $newNoRM = $tahunBulanSekarang . '-' . $paddedCounter;

        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);

        $insertQuery = "INSERT INTO pasien (nama, password, alamat, no_ktp, no_hp, no_rm) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insertQuery);
        $stmt->bind_param("ssssss", $newNamaPasien, $hashed_password, $newAlamat, $newNoKTP, $newNoHP, $newNoRM);

        if ($stmt->execute()) {
            header("Location: admin_Menumengelolapasien.php");
            exit();
        } else {
            echo "Insertion failed: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "Password tidak cocok";
        // Handle the error as needed
    }
}


if (isset($_POST['delete_pasien'])) {
    $id = $_POST['id'];

    $deletePasienQuery = "DELETE FROM pasien WHERE id=?";
    $stmtPasien = $mysqli->prepare($deletePasienQuery);
    $stmtPasien->bind_param("i", $id);

    if ($stmtPasien->execute()) {
        ob_clean();
        header("Location: admin_Menumengelolapasien.php");
        exit();
    } else {
        echo "Penghapusan Pasien gagal: " . $stmtPasien->error;
    }

    $stmtPasien->close();
}

$pasienQuery = "SELECT * FROM pasien";
$pasienResult = $mysqli->query($pasienQuery);
$pasienData = $pasienResult->fetch_all(MYSQLI_ASSOC);
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
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#addModal">Tambah Pasien</button>
<br><br>
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Pasien</th>
                                            <th>Alamat</th>
                                            <th>No KTP</th>
                                            <th>No HP</th>
                                            <th>No RM</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $counter = 1;
                                        foreach ($pasienData as $pasienRow) {
                                            echo "<tr>";
                                            echo "<td>" . $counter . "</td>";
                                            $counter++;
                                            echo "<td>" . $pasienRow['nama'] . "</td>";
                                            echo "<td>" . $pasienRow['alamat'] . "</td>";
                                            echo "<td>" . $pasienRow['no_ktp'] . "</td>";
                                            echo "<td>" . $pasienRow['no_hp'] . "</td>";
                                            echo "<td>" . $pasienRow['no_rm'] . "</td>";
                                            echo "<td>
                                                <form method='post' action=''>
                                                    <input type='hidden' name='id' value='" . $pasienRow['id'] . "'>
                                                    <input type='hidden' name='new_nama_pasien' value='" . $pasienRow['nama'] . "'>
                                                    <input type='hidden' name='new_alamat' value='" . $pasienRow['alamat'] . "'>
                                                    <input type='hidden' name='new_no_ktp' value='" . $pasienRow['no_ktp'] . "'>
                                                    <input type='hidden' name='new_no_hp' value='" . $pasienRow['no_hp'] . "'>
                                                    <input type='hidden' name='new_no_rm' value='" . $pasienRow['no_rm'] . "'>

                                                    <button type='button' name='update_pasien' class='btn btn-primary btn-sm update-btn' data-toggle='modal' data-target='#updateModal' 
                                                        data-id='" . $pasienRow['id'] . "' 
                                                        data-nama_pasien='" . $pasienRow['nama'] . "' 
                                                        data-password='" . $pasienRow['password'] . "' 
                                                        data-alamat='" . $pasienRow['alamat'] . "' 
                                                        data-no_ktp='" . $pasienRow['no_ktp'] . "' 
                                                        data-no_hp='" . $pasienRow['no_hp'] . "'
                                                        data-no_rm='" . $pasienRow['no_rm'] . "'>Update</button>
                                                    
                                                    <form method='post' action=''>
                                                        <input type='hidden' name='id' value='" . $pasienRow['id'] . "'>
                                                        <button type='submit' name='delete_pasien' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\");'>Delete</button>
                                                    </form>
                                                </form>
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

    <!-- Modal for updating Pasien -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Perbarui Pasien</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="admin_Menumengelolapasien.php">
                        <input type="hidden" name="id" id="update_id">
                        <div class="form-group">
                            <label for="update_nama_pasien">Nama Pasien</label>
                            <input type="text" class="form-control" id="update_nama_pasien" name="new_nama_pasien" required>
                        </div>
                        <div class="form-group">
                            <label for="update_alamat">Alamat</label>
                            <input type="text" class="form-control" id="update_alamat" name="new_alamat" required>
                        </div>
                        <div class="form-group">
                            <label for="update_no_ktp">No KTP</label>
                            <input type="text" class="form-control" id="update_no_ktp" name="new_no_ktp" required>
                        </div>
                        <div class="form-group">
                            <label for="update_no_hp">No HP</label>
                            <input type="text" class="form-control" id="update_no_hp" name="new_no_hp" required>
                        </div>
                        <button type="submit" name="update_pasien_modal" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding Pasien -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Pasien</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Correct the form opening tag -->
                    <form method="post" action="admin_Menumengelolapasien.php">
                        <div class="form-group">
                            <label for="add_nama_pasien">Nama Pasien</label>
                            <input type="text" class="form-control" id="add_nama_pasien" name="add_nama_pasien" required>
                        </div>
                        <div class="form-group">
                            <label for="add_password">Password</label>
                            <input type="password" class="form-control" id="add_password" name="add_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="form-group">
                            <label for="add_alamat">Alamat</label>
                            <input type="text" class="form-control" id="add_alamat" name="add_alamat" required>
                        </div>
                        <div class="form-group">
                            <label for="add_no_ktp">No KTP</label>
                            <input type="text" class="form-control" id="add_no_ktp" name="add_no_ktp" required>
                        </div>
                        <div class="form-group">
                            <label for="add_no_hp">No HP</label>
                            <input type="text" class="form-control" id="add_no_hp" name="add_no_hp" required>
                        </div>
                        <!-- Correct the closing tag of the form -->
                        <button type="submit" id="add_pasien_btn" name="add_pasien" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>

    <!-- Add other necessary script includes here -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var updateButtons = document.querySelectorAll('.update-btn');

            updateButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var id = button.getAttribute('data-id');
                    var nama_pasien = button.getAttribute('data-nama_pasien');
                    var password = button.getAttribute('data-password');
                    var alamat = button.getAttribute('data-alamat');
                    var no_ktp = button.getAttribute('data-no_ktp');
                    var no_hp = button.getAttribute('data-no_hp');
                    var no_rm = button.getAttribute('data-no_rm');

                    document.getElementById('update_id').value = id;
                    document.getElementById('update_nama_pasien').value = nama_pasien;
                    document.getElementById('update_alamat').value = alamat;
                    document.getElementById('update_no_ktp').value = no_ktp;
                    document.getElementById('update_no_hp').value = no_hp;
                });
            });
        });
    </script>

</body>

</html>