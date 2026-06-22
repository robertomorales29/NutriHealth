<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$user = current_user();
$returnTo = $_POST['return_to'] ?? 'dashboard.php';
$id = (int) ($_POST['id'] ?? 0);

$stmt = db()->prepare('SELECT * FROM historial_salud WHERE id = ?');
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record || !can_manage_health_history($user, (int) $record['paciente_id'])) {
    flash('error', 'No tienes permiso para eliminar este avance.');
    redirect($returnTo);
}

try {
    db()->beginTransaction();
    $delete = db()->prepare('DELETE FROM historial_salud WHERE id = ?');
    $delete->execute([$id]);
    sync_current_health_from_latest_history((int) $record['paciente_id']);
    db()->commit();
    flash('success', 'Avance eliminado correctamente.');
} catch (Throwable $e) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    flash('error', 'No se pudo eliminar el avance.');
}

redirect($returnTo);
