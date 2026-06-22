<?php
require_once __DIR__ . '/../includes/functions.php';
require_role(['entrenador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/entrenador/rutinas.php');
}

$user = current_user();
$returnTo = $_POST['return_to'] ?? 'pages/entrenador/rutinas.php';
$id = (int) ($_POST['id'] ?? 0);

$stmt = db()->prepare('SELECT re.id FROM rutina_ejercicios re INNER JOIN rutinas r ON r.id = re.rutina_id WHERE re.id = ? AND r.entrenador_id = ?');
$stmt->execute([$id, $user['id']]);
if (!$stmt->fetch()) {
    flash('error', 'Ejercicio no válido.');
    redirect($returnTo);
}

$delete = db()->prepare('DELETE FROM rutina_ejercicios WHERE id = ?');
$delete->execute([$id]);
flash('success', 'Ejercicio eliminado.');
redirect($returnTo);
