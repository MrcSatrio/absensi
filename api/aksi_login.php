<?php
// Cek apakah ada data yang dikirim dari formulir login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Koneksi ke database (sesuaikan dengan informasi koneksi Anda)
    include 'koneksi.php';

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Ambil data dari formulir login dan lakukan sanitasi
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // Query untuk mencari user berdasarkan username dan password
    $sql = "SELECT * FROM akun WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result) {
        // Periksa apakah ada hasil dari query
        if ($result->num_rows > 0) {
            // User ditemukan, set session dan arahkan ke halaman selanjutnya
            $row = $result->fetch_assoc();
            session_start();
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['id_role'] = $row['id_role']; // Menyimpan id_role ke dalam session
            if ($row['id_role'] == 1) {
                header("Location: admin/dashboard.php");
                exit();
            } elseif ($row['id_role'] == 2) {
                header("Location: user/dashboard.php");
                exit();
            } else {
                // Jika id_role tidak sesuai dengan yang diharapkan
                echo "Tidak ada akses yang diizinkan.";
                exit();
            }
        } else {
            // User tidak ditemukan
            echo "Username atau password salah.";
        }
    } else {
        // Query error
        echo "Error: " . $conn->error;
    }

    // Tutup koneksi
    $conn->close();
}
?>
