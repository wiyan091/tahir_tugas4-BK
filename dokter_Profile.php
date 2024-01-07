<?php
include 'db_koneksi.php';

// Mulai sesi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah dokter sudah login
if (!isset($_SESSION['nama'])) {
    header("Location: index.php"); // Redirect ke halaman login jika belum login
    exit();
}

// Ambil data dokter dari sesi
$dokter_id = $_SESSION['nama'];

// Ambil data dokter dari database (gunakan prepared statement)
$query = "SELECT * FROM dokter WHERE nama = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $dokter_id);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah data dokter ditemukan
if ($result->num_rows == 1) {
    $dokter = $result->fetch_assoc();

    // Proses form jika ada yang disubmit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Update nama
        $new_nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
        $new_id_poli = filter_input(INPUT_POST, 'id_poli', FILTER_VALIDATE_INT);

        // Update password
        $new_password = mysqli_real_escape_string($mysqli, $_POST['password']);
        $confirm_password = mysqli_real_escape_string($mysqli, $_POST['confirm_password']);


        if ($new_password != $confirm_password) {
            $error_message = "Password and Confirm Password do not match.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $query_update = "UPDATE dokter SET nama = ?, password = ?, id_poli = ? WHERE nama = ?";
            $stmt_update = $mysqli->prepare($query_update);
            $stmt_update->bind_param('ssss', $new_nama, $hashed_password, $new_id_poli, $dokter_id);
            $stmt_update->execute();


            // Update session nama setelah mengubah nama
            $_SESSION['nama'] = $new_nama;

            $success = true;
        }
    }
} else {
    // Jika data dokter tidak ditemukan, redirect atau tampilkan pesan error sesuai kebutuhan
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile.css">
</head>

<body>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($success)) {
            if ($success) {
                echo '<p class="success">Perubahan disimpan dengan sukses!</p>';
            } else {
                echo '<p class="error">' . $error_message . '</p>';
            }
        }
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()" name="profileForm">
        <label for="nama">Nama</label>
        <input type="text" name="nama" value="<?php echo $dokter['nama']; ?>" required><br>

        <label for="id_poli">Poli</label>
        <select name="id_poli" class="form-control" required>
            <?php
            $poliQuery = "SELECT id, nama_poli FROM poli";
            $poliResult = $mysqli->query($poliQuery);

            if ($poliResult) {
                while ($row = $poliResult->fetch_assoc()) {
                    $id = $row['id'];
                    $nama_poli = $row['nama_poli'];
                    $selected = ($dokter['id_poli'] == $id) ? 'selected' : '';
                    echo '<option value="' . $id . '" ' . $selected . '>' . $nama_poli . '</option>';
                }
                $poliResult->free();
            } else {
                echo 'Error: ' . $mysqli->error;
            }
            ?>
        </select>

        <label for="password">Password</label>
        <input type="password" name="password" required><br>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" required><br>

        <input type="submit" value="Simpan">
    </form>

    <script>
        function validateForm() {
            var nama = document.forms["profileForm"]["nama"].value;
            var password = document.forms["profileForm"]["password"].value;
            var confirm_password = document.forms["profileForm"]["confirm_password"].value;

            if (nama == "" || password == "" || confirm_password == "") {
                alert("Nama, Password, dan Confirm Password harus diisi");
                return false;
            }
        }
    </script>

</body>

</html>