<?php
if (!isset($_SESSION)) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $id_poli = $_POST['id_poli'];

    if ($password === $confirm_password) {
        $query = "SELECT * FROM dokter WHERE nama ='$nama'";
        $result = $mysqli->query($query);
        if ($result === false) {
            die("Query error: " . $mysqli->error);
        }

        if ($result->num_rows == 0) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert_query = "INSERT INTO dokter (nama, password, alamat, no_hp, id_poli) 
                            VALUES ('$nama', '$hashed_password', '$alamat', '$no_hp', '$id_poli')";

            if (mysqli_query($mysqli, $insert_query)) {
                echo "<script>
                alert('Pendaftaran Berhasil'); 
                document.location='index.php?page=dokter_Login';
                </script>";
            } else {
                $error = "Pendaftaran gagal";
            }
        } else {
            $error = "Username sudah digunakan";
        }
    } else {
        $error = "Password tidak cocok";
    }
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center" style="font-weight: bold; font-size: 32px;">Register</div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=dokter_Register">
                        <?php
                        if (isset($error)) {
                            echo '<div class="alert alert-danger">' . $error . '
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>';
                        }
                        ?>
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama anda">
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" name="alamat" class="form-control" required placeholder="Masukkan alamat anda">
                        </div>
                        <div class="form-group">
                            <label for="no_hp">Nomor HP</label>
                            <input type="text" name="no_hp" class="form-control" required placeholder="Masukkan nomor HP">
                        </div>
                        <div class="form-group">
                            <label for="id_poli">ID Poli</label>
                            <select name="id_poli" class="form-control" required>
                                <?php
                                // Assuming $mysqli is your database connection
                                $poliQuery = "SELECT id, nama_poli FROM poli";
                                $poliResult = $mysqli->query($poliQuery);

                                // Check if query executed successfully
                                if ($poliResult) {
                                    while ($row = $poliResult->fetch_assoc()) {
                                        $id = $row['id'];
                                        $nama_poli = $row['nama_poli'];
                                        echo '<option value="' . $id . '">' . $nama_poli . '</option>';
                                    }

                                    // Free the result set
                                    $poliResult->free();
                                } else {
                                    echo 'Error: ' . $mysqli->error;
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required placeholder="Masukkan password konfirmasi">
                        </div>
                        <br>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>