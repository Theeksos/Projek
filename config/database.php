<?php
/**
 * File: config/database.php
 * Fungsi: Menghubungkan aplikasi PHP ke database MySQL (Data Layer)
 * Ini adalah "jembatan" antara kode PHP dan database.
 */

// --- Konfigurasi koneksi (sesuaikan dengan XAMPP/Laragon kamu) ---
$host   = "localhost";
$dbname = "doughco_hub";
$user   = "root";
$pass   = ""; // default XAMPP/Laragon kosong

try {
    // Menggunakan PDO (PHP Data Objects) → lebih aman dari mysqli biasa
    $koneksi = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);

    // Supaya PDO melempar error yang jelas kalau ada masalah query
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Jika koneksi gagal, hentikan proses dan tampilkan pesan
    die("Koneksi database gagal: " . $e->getMessage());
}
