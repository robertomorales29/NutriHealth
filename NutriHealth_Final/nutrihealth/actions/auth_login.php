<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

if ($login === '' || $password === '') {
    flash('error', 'Captura usuario/correo y contraseña.');
    redirect('login.php');
}

$stmt = db()->prepare('SELECT * FROM personas WHERE (email = ? OR usuario = ?) AND activo = 1 LIMIT 1');
$stmt->execute([$login, $login]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    flash('error', 'Credenciales incorrectas.');
    redirect('login.php');
}

$_SESSION['user'] = [
    'id' => (int) $user['id'],
    'nombre' => $user['nombre'],
    'email' => $user['email'],
    'usuario' => $user['usuario'],
    'rol' => $user['rol'],
];

flash('success', 'Bienvenido a NutriHealth, ' . $user['nombre'] . '.');
redirect('dashboard.php');
