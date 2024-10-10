<?php
// Password yang ingin di-hash
$password = 'Admin#123';

// Menghasilkan hash password dengan bcrypt
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Tampilkan hash password
echo $hashedPassword;
?>


$2y$10$hvxp/vR1fxAqn7QTd6QRee8OvizCaBSEV4YPgp1cSzzVZtt53s6U6