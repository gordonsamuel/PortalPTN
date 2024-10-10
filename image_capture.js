const video = document.querySelector('#video');
const canvas = document.querySelector('#canvas');
const captureButton = document.querySelector('#captureButton');
const imageInput = document.querySelector('#imageInput');

navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        video.srcObject = stream;
    })
    .catch(err => {
        console.error('Error accessing camera: ', err);
    });

captureButton.addEventListener('click', () => {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    // Mengambil gambar dari canvas sebagai data URL
    const imageDataUrl = canvas.toDataURL('image/png');
    
    // Menampilkan data URL ke input form (sembunyikan input ini di HTML)
    imageInput.value = imageDataUrl;
});

// Submit form dan kirim gambar ke server
