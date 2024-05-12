<?php
// Pastikan file ini disimpan di server Anda dengan nama endcam.php

// Lokasi folder untuk menyimpan file yang diterima
$uploadDir = 'uploads/';

// Pastikan folder upload sudah ada atau buat jika belum ada
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imageFile'])) {
    $fileTmpPath = $_FILES['imageFile']['tmp_name'];
    $fileName = $_FILES['imageFile']['name'];
    $fileSize = $_FILES['imageFile']['size'];
    $fileType = $_FILES['imageFile']['type'];

    // Tentukan path lengkap untuk menyimpan file
    $newFilePath = $uploadDir . $fileName;

    // Pindahkan file dari tempat sementara ke lokasi tujuan
    if (move_uploaded_file($fileTmpPath, $newFilePath)) {
        echo "File berhasil diunggah: " . $newFilePath;
    } else {
        echo "Gagal mengunggah file.";
    }
} else {
    echo "Permintaan tidak valid.";
}
?>
