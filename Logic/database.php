<?php
// Konfigurasi Database Dough & Co
$host     = "localhost";
$username = "root";
$password = ""; // Kosongkan jika menggunakan XAMPP bawaan
$dbname   = "doughco_hub"; // Sesuaikan dengan nama database kamu di phpMyAdmin

try {
    // Membuat koneksi database menggunakan driver PDO MySQL
    $koneksi = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Mengatur error mode PDO ke Exception agar jika ada query salah langsung ketahuan error-nya
    $koneksi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mengatur default fetch mode ke Associative Array agar lebih praktis
    $koneksi->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Jika koneksi gagal, sistem akan stop dan menampilkan pesan error murni
    die("Koneksi ke database Dough & Co gagal: " . $e->getMessage());
}
?>