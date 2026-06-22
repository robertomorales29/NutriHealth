<?php
require_once __DIR__ . '/../includes/functions.php';
require_role(['entrenador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/entrenador/rutinas.php');
}

$user = current_user();
$id = (int) ($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if ($nombre === '') {
    flash('error', 'El nombre de la rutina es obligatorio.');
    redirect('pages/entrenador/rutinas.php');
}

if ($id > 0) {
    $stmt = db()->prepare('UPDATE rutinas SET nombre = ?, descripcion = ? WHERE id = ? AND entrenador_id = ?');
    $stmt->execute([$nombre, $descripcion ?: null, $id, $user['id']]);
    flash('success', 'Rutina actualizada correctamente.');
} else {
    $stmt = db()->prepare('INSERT INTO rutinas (nombre, descripcion, entrenador_id) VALUES (?, ?, ?)');
    $stmt->execute([$nombre, $descripcion ?: null, $user['id']]);
    flash('success', 'Rutina creada correctamente.');
}

redirect('pages/entrenador/rutinas.php');
