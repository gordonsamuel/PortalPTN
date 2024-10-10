<?php
include 'database.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

// Check if user has already submitted data
$username = $_SESSION['username'];
$sql_check = "SELECT * FROM students WHERE username = ?";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute([$username]);
$existingSubmission = $stmt_check->fetch();

if ($existingSubmission && !isset($_POST['username'])) {
    // Redirect to the biodata page only if no form submission detected
    header("Location: biodata.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $name = $_POST['name'];
    $birth_place_date = $_POST['birth_place_date'];
    $address = $_POST['address'];
    $school_origin = $_POST['school_origin'];
    $graduation_year = $_POST['graduation_year'];
    $parent_name = $_POST['parent_name'];
    $major_id = $_POST['major_id'];

    // Mengambil username dari session
    $username = $_SESSION['username'];

    // Menghasilkan nomor pendaftaran otomatis
    $registration_number = 'REG' . time();

    // Upload gambar
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    } else {
        // Jika gambar diambil dari kamera, gunakan data base64
        if (!empty($_POST['image'])) {
            $imageData = $_POST['image'];
            $image = uniqid() . '.png'; // Generate nama file
            $target_file = "uploads/" . $image;

            // Decode base64 dan simpan ke file
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            file_put_contents($target_file, base64_decode($imageData));
        }
    }

    // Menyimpan data ke database
    $sql = "INSERT INTO students (name, birth_place_date, address, school_origin, graduation_year, parent_name, major_id, registration_number, image_path, username) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $birth_place_date, $address, $school_origin, $graduation_year, $parent_name, $major_id, $registration_number, $target_file, $username]);

    // Menyimpan data ke session untuk ditampilkan di biodata.php
    $_SESSION['student_data'] = [
        'name' => $name,
        'birth_place_date' => $birth_place_date,
        'address' => $address,
        'school_origin' => $school_origin,
        'graduation_year' => $graduation_year,
        'parent_name' => $parent_name,
        'major_id' => $major_id,
        'registration_number' => $registration_number,
        'image_path' => $target_file
    ];

    // Redirect ke biodata.php
    header("Location: biodata.php");
    exit();
} else {
    // Jika formulir belum diposting, inisialisasi nomor pendaftaran
    $registration_number = 'REG' . time(); // Atur nilai default
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

    <title>Form Pendaftaran Mahasiswa Baru</title>
    <style>
        /* Gaya untuk video dalam modal */
        #video {
            border: 1px solid #ccc; /* Border untuk video */
            border-radius: 4px; /* Sudut melengkung */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Bayangan */
            width: 100%; /* Lebar penuh */
        }

        /* Gaya untuk gambar yang ditangkap */
        #capturedImage {
            display: none; /* Awalnya disembunyikan */
            margin-top: 20px; /* Jarak atas */
            border: 1px solid #ccc; /* Border untuk gambar */
            border-radius: 4px; /* Sudut melengkung */
            width: 100%; /* Lebar penuh */
        }

        /* Memastikan modal responsif */
        .modal-lg {
            max-width: 90%; /* Lebar maksimum modal */
        }
    </style>
</head>
<body class="container">
    <h2 class="mt-4">Pendaftaran Mahasiswa Baru</h2>
    <form method="POST" enctype="multipart/form-data" action="">
    <div class="mb-3">
            <label for="registration_number" class="form-label">Nomor Pendaftaran</label>
            <input type="text" class="form-control" id="registration_number" name="registration_number" value="<?php echo $registration_number; ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Nama Calon Mahasiswa Baru</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="birth_place_date" class="form-label">Tempat Tanggal Lahir</label>
            <input type="text" class="form-control" id="birth_place_date" name="birth_place_date" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Alamat</label>
            <textarea class="form-control" id="address" name="address" required></textarea>
        </div>
        <div class="mb-3">
            <label for="school_origin" class="form-label">Asal SMA</label>
            <input type="text" class="form-control" id="school_origin" name="school_origin" required>
        </div>
        <div class="mb-3">
            <label for="graduation_year" class="form-label">Tahun Lulus</label>
            <input type="number" class="form-control" id="graduation_year" name="graduation_year" required>
        </div>
        <div class="mb-3">
            <label for="parent_name" class="form-label">Nama Orang Tua</label>
            <input type="text" class="form-control" id="parent_name" name="parent_name" required>
        </div>
        <div class="mb-3">
         <label for="major_id" class="form-label">Jurusan</label>
        <select class="form-select" id="major_id" name="major_id" required>
        <?php
        // Fetch the list of majors from the database
        $sql = "SELECT * FROM majors";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $majors = $stmt->fetchAll();
        
        // Loop through the majors and create options
        foreach ($majors as $major) {
            echo "<option value='{$major['id']}'>{$major['major_name']}</option>";
        }
        ?>
        </select>
</div>
        <div class="mb-3">
            <label for="camera" class="form-label">Ambil Foto</label>
            <button type="button" class="btn btn-secondary mt-2" data-bs-toggle="modal" data-bs-target="#cameraModal">Capture</button>
            <input type="hidden" id="imageInput" name="image" required>
            <img id="capturedImage" style="width: 300px; height: 400px; display:none; margin-top: 20px;" />
   </div>

       <!-- Modal untuk Kamera -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;"> <!-- Atur lebar maksimum modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cameraModalLabel">Ambil Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <video id="video" autoplay playsinline style="width: 100%; height: auto;"></video> <!-- Memastikan video responsif -->
                <canvas id="canvas" style="display: none;"></canvas>
                <button id="captureButton" class="btn btn-primary mt-2">Capture</button>
            </div>
        </div>
    </div>
</div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const video = document.querySelector('#video');
        const captureButton = document.querySelector('#captureButton');
        const imageInput = document.querySelector('#imageInput');
        const capturedImage = document.querySelector('#capturedImage');

        // Minta akses ke kamera
        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => {
                    console.error('Error accessing camera: ', err);
                    alert('Tidak dapat mengakses kamera: ' + err.message);
                });
        }

       // Event listener untuk menangkap gambar
captureButton.addEventListener('click', () => {
    const canvas = document.querySelector('#canvas');
    const context = canvas.getContext('2d');
    const aspectRatio = 3 / 4; // Rasio 3:4

    // Mengatur ukuran canvas untuk 3:4
    canvas.width = 300; // Lebar
    canvas.height = 400; // Tinggi

    // Menghitung posisi dan ukuran gambar untuk cropping
    const videoWidth = video.videoWidth;
    const videoHeight = video.videoHeight;

    const videoAspectRatio = videoWidth / videoHeight;

    let drawWidth, drawHeight, drawX, drawY;

    // Menentukan ukuran gambar berdasarkan rasio
    if (videoAspectRatio > aspectRatio) {
        // Video lebih lebar dari rasio 3:4
        drawHeight = canvas.height;
        drawWidth = drawHeight * videoAspectRatio;
        drawX = (drawWidth - canvas.width) / 2; // Pusatkan gambar
        drawY = 0;
    } else {
        // Video lebih tinggi dari rasio 3:4
        drawWidth = canvas.width;
        drawHeight = drawWidth / videoAspectRatio;
        drawX = 0;
        drawY = (drawHeight - canvas.height) / 2; // Pusatkan gambar
    }

    // Menggambar video ke canvas dengan crop
    context.drawImage(video, drawX, drawY, drawWidth, drawHeight, 0, 0, canvas.width, canvas.height);


            const imageDataUrl = canvas.toDataURL('image/png');
            imageInput.value = imageDataUrl; // Simpan gambar ke input
            capturedImage.src = imageDataUrl; // Tampilkan gambar yang diambil
            capturedImage.style.display = 'block'; // Tampilkan gambar

            // Tutup modal setelah capture
            const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
            modal.hide(); // Tutup modal
        });

        // Mulai kamera saat modal dibuka
        const cameraModal = document.getElementById('cameraModal');
        cameraModal.addEventListener('shown.bs.modal', startCamera);
    </script>

        <div class="mb-3">
            <label for="image" class="form-label">Upload Foto</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</body>
</html>
