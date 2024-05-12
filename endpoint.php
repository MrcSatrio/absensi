<?php
$host = 'localhost';
$dbname = 'tes';
$username = 'root';
$password = '';

// Koneksi ke database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}

// Memproses data POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pastikan data kartu dan link ada
    if (isset($_POST['kartu']) && isset($_POST['link'])) {
        // Ambil data kartu dan link dari POST
        $nomorKartu = $_POST['kartu'];
        $link = $_POST['link'];

        // Cek kartu pada tabel 'kartu'
        $sqlCheckKartu = "SELECT id_kartu FROM kartu WHERE nomor_kartu = :nomor_kartu";
        $stmtCheckKartu = $pdo->prepare($sqlCheckKartu);
        $stmtCheckKartu->bindParam(':nomor_kartu', $nomorKartu);
        $stmtCheckKartu->execute();
        $resultKartu = $stmtCheckKartu->fetch(PDO::FETCH_ASSOC);

        if ($resultKartu) {
            $idKartu = $resultKartu['id_kartu'];

            // Cek id_kartu pada tabel 'akun'
            $sqlCheckAkun = "SELECT id_user, nama FROM akun WHERE id_kartu = :id_kartu";
            $stmtCheckAkun = $pdo->prepare($sqlCheckAkun);
            $stmtCheckAkun->bindParam(':id_kartu', $idKartu);
            $stmtCheckAkun->execute();
            $resultAkun = $stmtCheckAkun->fetch(PDO::FETCH_ASSOC);

            if ($resultAkun) {
                $idUser = $resultAkun['id_user'];
                $namaPengguna = $resultAkun['nama'];

                // Cek absensi pada hari yang sama
                $sqlCheckAbsensi = "SELECT id_absen, jam_pulang FROM absen WHERE id_user = :id_user AND DATE(created_at) = CURDATE()";
                $stmtCheckAbsensi = $pdo->prepare($sqlCheckAbsensi);
                $stmtCheckAbsensi->bindParam(':id_user', $idUser);
                $stmtCheckAbsensi->execute();
                $existingAbsensi = $stmtCheckAbsensi->fetch(PDO::FETCH_ASSOC);

                if ($existingAbsensi) {
                    // Kartu sudah melakukan absensi pada hari yang sama
                    if ($existingAbsensi['jam_pulang'] === null) {
                        // Update jam_pulang saat ini
                        $sqlUpdateJamPulang = "UPDATE absen SET jam_pulang = NOW(), foto_pulang = :foto WHERE id_user = :id_user AND DATE(created_at) = CURDATE()";
                        $stmtUpdateJamPulang = $pdo->prepare($sqlUpdateJamPulang);
                        $stmtUpdateJamPulang->bindParam(':id_user', $idUser);
                        $stmtUpdateJamPulang->bindParam(':foto', $link);

                        try {
                            $stmtUpdateJamPulang->execute();
                            echo "Absen Pulang $namaPengguna.";
                        } catch (PDOException $e) {
                            echo "Gagal mengupdate jam pulang: " . $e->getMessage();
                        }
                    } else {
                        echo "Anda Sudah Absen Hari Ini";
                    }
                } else {
                    // Kartu belum melakukan absensi hari ini
                    // Lakukan operasi insert ke tabel 'absen'
                    $sqlInsert = "INSERT INTO absen (id_user, jam_masuk, foto_masuk, created_at, updated_at) 
                                  VALUES (:id_user, NOW(), :foto, NOW(), NOW())";
                    $stmtInsert = $pdo->prepare($sqlInsert);
                    $stmtInsert->bindParam(':id_user', $idUser);
                    $stmtInsert->bindParam(':foto', $link);

                    try {
                        $stmtInsert->execute();
                        echo "Absen Masuk $namaPengguna.";
                    } catch (PDOException $e) {
                        echo "Gagal menyimpan data ke database: " . $e->getMessage();
                    }
                }
            } else {
                echo "Id user tidak ditemukan dalam tabel akun.";
            }
        } else {
            echo "Nomor kartu tidak ditemukan dalam tabel kartu.";
        }
    } else {
        echo "Data kartu dan link harus disertakan dalam request POST.";
    }
} else {
    echo "Metode request harus POST.";
}
?>