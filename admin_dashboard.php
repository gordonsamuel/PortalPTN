<?php
include 'database.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Mengambil data mahasiswa dari database
$sql = "SELECT students.*, majors.major_name FROM students 
        JOIN majors ON students.major_id = majors.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style4.css" rel="stylesheet">
    <title>Dashboard Admin</title>
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


    <h2 class="mt-4">Dashboard Admin</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tempat, Tanggal Lahir</th>
                <th>Asal Sekolah</th>
                <th>Jurusan</th>
                <th>Nomor Pendaftaran</th>
                <th>Status</th>
                <th>Foto</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $index => $student): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= $student['name'] ?></td>
                <td><?= $student['birth_place_date'] ?></td>
                <td><?= $student['school_origin'] ?></td>
                <td><?= $student['major_name'] ?></td>
                <td><?= $student['registration_number'] ?></td>
                <td><?= ucfirst($student['status']) ?></td>
                <td><img src="<?= $student['image_path'] ?>" alt="Foto" width="100"></td>
                <td>
                    <form method="POST" action="update_status.php">
                        <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
                        <select name="status" class="form-select">
                            <option value="pass" <?= $student['status'] == 'pass' ? 'selected' : '' ?>>Lulus</option>
                            <option value="fail" <?= $student['status'] == 'fail' ? 'selected' : '' ?>>Gagal</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm mt-2">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
