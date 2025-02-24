<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sekolah";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Koneksi gagal: " . $conn->connect_error]));
}

// Pastikan metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<div style='text-align: center; margin-top: 20px;'>";
    echo "<h2>Daftar Data Siswa</h2>";
    echo "<h2>Smkn 1 Masbagik</h2>";
    
    $query = "SELECT no, nisn, nama, kelas, jurusan, tanggal_lahir, alamat, jenis_kelamin FROM siswa ORDER BY no ASC LIMIT 100";
    $result = $conn->query($query);
    
    if ($result) {
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0' style='margin: 0 auto; text-align: center;'>";
            echo "<tr><th>No</th><th>NISN</th><th>Nama</th><th>Kelas</th><th>Jurusan</th><th>Tanggal Lahir</th><th>Alamat</th><th>Jenis Kelamin</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['no']}</td><td>{$row['nisn']}</td><td>{$row['nama']}</td><td>{$row['kelas']}</td><td>{$row['jurusan']}</td><td>{$row['tanggal_lahir']}</td><td>{$row['alamat']}</td><td>{$row['jenis_kelamin']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Tidak ada data tersimpan.</p>";
        }
    } else {
        echo "<p>Terjadi kesalahan dalam mengambil data: " . $conn->error . "</p>";
    }
    echo "</div>";
    exit;
}

// Ambil data dari form
$nisn = isset($_POST['nisn']) ? trim($_POST['nisn']) : '';
$nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
$kelas = isset($_POST['kelas']) ? trim($_POST['kelas']) : '';
$jurusan = isset($_POST['jurusan']) ? trim($_POST['jurusan']) : '';
$tanggal_lahir = isset($_POST['tanggal_lahir']) ? trim($_POST['tanggal_lahir']) : '';
$alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
$jenis_kelamin = isset($_POST['jenis_kelamin']) ? trim($_POST['jenis_kelamin']) : '';

// Validasi: Pastikan semua data diisi
if (empty($nisn) || empty($nama) || empty($kelas) || empty($jurusan) || empty($tanggal_lahir) || empty($alamat) || empty($jenis_kelamin)) {
    echo json_encode(["status" => "error", "message" => "Semua kolom harus diisi!"]);
    exit;
}

// Query untuk menyimpan data
$sql = "INSERT INTO siswa (nisn, nama, kelas, jurusan, tanggal_lahir, alamat, jenis_kelamin) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $nisn, $nama, $kelas, $jurusan, $tanggal_lahir, $alamat, $jenis_kelamin);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Data berhasil disimpan!"]);
} else {
    if ($conn->errno == 1062) {
        echo json_encode(["status" => "error", "message" => "NISN sudah ada!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Terjadi kesalahan: " . $conn->error]);
    }
}

$stmt->close();
$conn->close();
?>
