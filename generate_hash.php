<?php
// GANTI 'password_yang_anda_ingat' dengan password yang ingin Anda gunakan!
$password_plain = '12345';
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

echo "Password Asli Anda: " . $password_plain . "<br>";
echo "Password HASH yang akan disimpan ke database: " . $password_hashed . "<br>";
?>