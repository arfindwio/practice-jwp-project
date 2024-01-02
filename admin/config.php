<?php

// Informasi koneksi ke database
$servername = "localhost"; // Nama server database
$username = "root"; // Nama pengguna database
$password = ""; // Kata sandi pengguna database
$dbname = "arfindwioctavianto"; // Nama database yang digunakan

// Membuat objek koneksi menggunakan MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa apakah koneksi berhasil atau gagal
if ($conn->connect_error) {
    // Jika koneksi gagal, tampilkan pesan kesalahan dan hentikan eksekusi skrip
    die("Connection failed: " . $conn->connect_error);
}
