<?php
require_once __DIR__ . '/../includes/functions.php';
require_role(['entrenador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/entrenador/rutinas.php');
}

$id = (int) ($_POST['id'] ?? 0);
$user = current_user();
try {
    $stmt = db()->prepare('DELETE FROM rutinas WHERE id = ? AND entrenador_id = ?');
    $stmt->execute([$id, $user['id']]);
    flash('success', 'Rutina eliminada correctamente.');
} catch (Throwable $e) {
    flash('error', 'No se pudo eliminar. Si ya está asignada, cambia su estado o elimina la asignación primero.');
}
redirect('pages/entrenador/rutinas.php');
