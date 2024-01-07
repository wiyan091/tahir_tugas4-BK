<?php
function logout($redirectPage) {
    session_start();
    if (isset($_SESSION['username']) || isset($_SESSION['nama'])) {
        session_unset();
        session_destroy();
    }

    header("Location: index.php?page=$redirectPage");
    exit();
}

// Usage examples
logout("admin_Login");
logout("dokter_Login");
logout("pasien_Login");
?>
