<?php
if (!isset($_SESSION)) {
    session_start();
}

include_once("db_koneksi.php");
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rs-Elizabet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Add your logo -->
    <link rel="icon" type="image/png" href="els.png">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Rs-Elizabet</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.php">Dashboard</a>
                    </li>
                </ul>

                <?php
                if (isset($_SESSION['username' . 'nama'])) {
                    // Jika pengguna sudah login, tampilkan tombol "Logout"
                ?>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="Logout.php">Logout (<?php echo $_SESSION['username' . 'nama'] ?>)</a>
                        </li>
                    </ul>
                <?php
                } else {
                    // Jika pengguna belum login, tampilkan tombol "Login" dan "Register"
                ?>
                    <ul class="navbar-nav ms-auto">

                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=admin_Login">Login Admin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=dokter_Login">| Login Dokter</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=pasien_login">| Login Pasien</a>
                        </li>
                    </ul>
                <?php
                }
                ?>

            </div>
        </div>
    </nav>

    <?php
    if (isset($_GET['page'])) {
        // Check if the requested page is login, and if so, include admin_Login.php
        if ($_GET['page'] === 'login') {
            include("admin_Login.php");
        } elseif ($_GET['page'] === 'admin_Login') {
            // Redirect to admin_Login.php for loginUser page
            include("admin_Login.php");
        } elseif ($_GET['page'] === 'pasien_Login') {
            include("pasien_Login.php");
        } else {
            // Include other pages based on the value of $_GET['page']
            include($_GET['page'] . ".php");
        }
    } else {
        echo "<br><br><br><br><br><br><br><br><br><br><center><h2>Selamat Datang di Rs-Elizabet";
        echo "</h2><hr><center>Silakan Login untuk menggunakan layanan kami, jika belum memiliki akun silakan Register terlebih dahulu.</center>";
    }


    ?>
</body>

</html>