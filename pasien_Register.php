<?php
if (!isset($_SESSION)) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $alamat = $_POST['alamat'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];

    if ($password === $confirm_password) {
        // Membuat tahun dan bulan saat ini (contoh, 202312)
        $tahunBulanSekarang = date('Ym');

        // Mencari nomor rekam medis (no_rm) terakhir
        $queryLastNoRM = "SELECT MAX(SUBSTRING(no_rm, 9)) AS max_counter FROM pasien";
        $resultLastNoRM = $mysqli->query($queryLastNoRM);

        if ($resultLastNoRM === false) {
            die("Error query: " . $mysqli->error);
        }

        $counter = 1; // Nilai default jika tidak ada rekam medis yang ditemukan
        if ($resultLastNoRM->num_rows > 0) {
            $row = $resultLastNoRM->fetch_assoc();
            $counter = (int)$row['max_counter'] + 1;
        }

        // Pad nilai counter dengan leading zeros (contoh, 001, 002, ...)
        $paddedCounter = str_pad($counter, 3, '0', STR_PAD_LEFT);

        // Gabungkan tahunBulanSekarang dan paddedCounter untuk membentuk no_rm akhir
        $no_rm = $tahunBulanSekarang . '-' . $paddedCounter;

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insert_query = "INSERT INTO pasien (nama, password, alamat, no_ktp, no_hp, no_rm) 
                        VALUES ('$nama', '$hashed_password', '$alamat', '$no_ktp', '$no_hp', '$no_rm')";

        if (mysqli_query($mysqli, $insert_query)) {
            echo "<script>
            alert('Pendaftaran Berhasil'); 
            document.location='index.php?page=pasien_Login';
            </script>";
        } else {
            $error = "Pendaftaran gagal";
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
                    <form method="POST" action="index.php?page=pasien_Register">
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
                            <label for="no_ktp">Nomor KTP</label>
                            <input type="text" name="no_ktp" class="form-control" required placeholder="Masukkan No KTP">
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