<?php
include 'database.php';
session_start();

// Check if user is logged in and has the correct role
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}


// Fetch student data along with major name from the database
$sql = "
    SELECT students.*, majors.major_name 
    FROM students
    LEFT JOIN majors ON students.major_id = majors.id
    WHERE students.username = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['username']]);
$student_data = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array

// // Debugging: Lihat apa yang diambil dari query
// echo '<pre>';
// var_dump($student_data); // Tampilkan semua isi dari $student_data
// echo '</pre>';

// Jika student_data tidak ditemukan
if (!$student_data) {
    echo "No student data found for this user.";
    exit();
}

// // Fetch the submitted biodata from session
// if (!isset($_SESSION['student_data'])) {
//     header("Location: index.php"); // If no data in session, redirect to index.php
//     exit();
// }

// $student_data = $_SESSION['student_data'];

// Fetch status (determined by the admin)
$sql_status = "SELECT status FROM students WHERE registration_number = ?";
$stmt_status = $pdo->prepare($sql_status);
$stmt_status->execute([$student_data['registration_number']]);
$status = $stmt_status->fetchColumn();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style3.css">

</head>
<body class="container">

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Biodata Mahasiswa</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a> <!-- Link ke logout.php -->
                </li>
            </ul>
        </div>
    </div>
</nav>


    <h2 class="mt-4">Biodata Mahasiswa</h2>
    <ul class="list-group">
        <li class="list-group-item"><strong>Nama:</strong> <?= htmlspecialchars($student_data['name']); ?></li>
        <li class="list-group-item"><strong>Tempat Tanggal Lahir:</strong> <?= htmlspecialchars($student_data['birth_place_date']); ?></li>
        <li class="list-group-item"><strong>Alamat:</strong> <?= htmlspecialchars($student_data['address']); ?></li>
        <li class="list-group-item"><strong>Asal SMA:</strong> <?= htmlspecialchars($student_data['school_origin']); ?></li>
        <li class="list-group-item"><strong>Tahun Lulus:</strong> <?= htmlspecialchars($student_data['graduation_year']); ?></li>
        <li class="list-group-item"><strong>Nama Orang Tua:</strong> <?= htmlspecialchars($student_data['parent_name']); ?></li>
        <li class="list-group-item">
            <strong>Jurusan:</strong> 
            <?= htmlspecialchars($student_data['major_name']); ?>
        </li> <li class="list-group-item"><strong>Nomor Pendaftaran:</strong> <?= htmlspecialchars($student_data['registration_number']); ?></li>
        <li class="list-group-item"><strong>Foto:</strong><br><img src="<?= htmlspecialchars($student_data['image_path']); ?>" alt="Foto Mahasiswa" style="width: 200px; height: auto;"></li>
    </ul>

    <h3 class="mt-4">Status Pendaftaran</h3>
    <div class="alert alert-info">
        <strong>Status:</strong> <?= htmlspecialchars($status ? $status : 'Menunggu keputusan'); ?>
    </div>
</body>
</html>