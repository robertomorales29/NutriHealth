<?php
require_once __DIR__ . '/../includes/functions.php';
require_role(['nutriologo']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/nutriologo/dietas.php');
}

$user = current_user();
$id = (int) ($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$indicaciones = trim($_POST['indicaciones'] ?? '');
$returnTo = $_POST['return_to'] ?? 'pages/nutriologo/dietas.php';

if ($nombre === '') {
    flash('error', 'El nombre de la dieta es obligatorio.');
    redirect($returnTo);
}

if ($id > 0) {
    $stmt = db()->prepare('UPDATE dietas SET nombre = ?, descripcion = ?, indicaciones = ? WHERE id = ? AND nutriologo_id = ?');
    $stmt->execute([$nombre, $descripcion ?: null, $indicaciones ?: null, $id, $user['id']]);
    flash('success', 'Dieta actualizada correctamente.');
} else {
    $stmt = db()->prepare('INSERT INTO dietas (nombre, descripcion, indicaciones, nutriologo_id) VALUES (?, ?, ?, ?)');
    $stmt->execute([$nombre, $descripcion ?: null, $indicaciones ?: null, $user['id']]);
    flash('success', 'Dieta creada correctamente.');
}

redirect($returnTo);
