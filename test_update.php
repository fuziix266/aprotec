<?php
$hash = password_hash('123456', PASSWORD_BCRYPT);
$pdo = new PDO('mysql:dbname=castorypollux;host=localhost;charset=utf8', 'root', '');
$stmt = $pdo->prepare('UPDATE qr_usuarios SET password_hash = ? WHERE correo = ?');
$stmt->execute([$hash, 'admin@municipalidadarica.cl']);
echo 'Updated successfully';
